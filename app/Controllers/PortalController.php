<?php

namespace App\Controllers;

use App\Libraries\WhatsAppService;
use App\Models\AppSettingModel;
use App\Models\FamilyPortalAccessModel;

class PortalController extends BaseController
{
    public function updateFiles(int $accessId)
    {
        if (! hasPermission('documents.manage') && ! hasPermission('users.manage')) {
            return redirect()->back()->with('error', 'Sem permissão para configurar arquivos.');
        }

        $access = (new FamilyPortalAccessModel())->find($accessId);
        if (! $access) {
            return redirect()->back()->with('error', 'Acesso familiar não encontrado.');
        }

        $db = db_connect();
        $db->table('family_portal_files')->where('portal_access_id', $accessId)->delete();

        foreach ((array) $this->request->getPost('files') as $file) {
            [$type, $id] = array_pad(explode(':', $file, 2), 2, null);
            if (! in_array($type, ['contract', 'document'], true) || ! $id) {
                continue;
            }

            $db->table('family_portal_files')->insert([
                'portal_access_id' => $accessId,
                'file_type' => $type,
                'file_id' => (int) $id,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->back()->with('success', 'Arquivos da área da família atualizados.');
    }

    public function sendFamilyAccess(int $accessId)
    {
        if (! hasPermission('whatsapp.send') && ! hasPermission('documents.manage')) {
            return redirect()->back()->with('error', 'Sem permissão para enviar WhatsApp.');
        }

        $access = (new FamilyPortalAccessModel())->find($accessId);
        if (! $access) {
            return redirect()->back()->with('error', 'Acesso familiar não encontrado.');
        }

        $data = db_connect()->table('family_portal_accesses fpa')
            ->select('fpa.*, guardians.phone, patients.name AS acolhido_name')
            ->join('guardians', 'guardians.id = fpa.guardian_id')
            ->join('treatments', 'treatments.id = fpa.treatment_id')
            ->join('patients', 'patients.id = treatments.patient_id')
            ->where('fpa.id', $accessId)
            ->get()
            ->getRowArray();

        if (empty($data['phone'])) {
            return redirect()->back()->with('error', 'Responsável sem telefone cadastrado.');
        }

        $template = (new AppSettingModel())->value('whatsapp_family_portal_template', '');
        $message = str_replace(
            ['{{acolhido}}', '{{link}}', '{{senha}}'],
            [$data['acolhido_name'], $data['access_url'], $data['initial_password']],
            $template
        );

        $result = (new WhatsAppService())->sendText($data['phone'], $message);

        db_connect()->table('whatsapp_logs')->insert([
            'channel' => 'whatsapp',
            'recipient' => $data['phone'],
            'message' => $message,
            'status' => $result['ok'] ? 'sent' : 'failed',
            'provider_response' => is_string($result['response']) ? $result['response'] : json_encode($result['response']),
            'created_by' => session('user.id'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if ($result['ok']) {
            (new FamilyPortalAccessModel())->update($accessId, [
                'last_sent_at' => date('Y-m-d H:i:s'),
                'last_sent_to' => $data['phone'],
            ]);

            return redirect()->back()->with('success', 'Link da familia enviado por WhatsApp.');
        }

        return redirect()->back()->with('error', 'WhatsApp nao enviado: ' . $result['response']);
    }
}
