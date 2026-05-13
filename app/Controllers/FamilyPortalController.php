<?php

namespace App\Controllers;

use App\Models\FamilyPortalAccessModel;
use App\Models\AppSettingModel;
use App\Models\ScheduleItemModel;

class FamilyPortalController extends BaseController
{
    public function login(string $token)
    {
        $access = (new FamilyPortalAccessModel())->where('token', $token)->where('active', 1)->first();
        if (! $access) {
            return redirect()->to('/login')->with('error', 'Acesso familiar nao encontrado.');
        }

        if ($this->request->getMethod() === 'POST') {
            if (password_verify((string) $this->request->getPost('password'), $access['password_hash'])) {
                session()->set('family_portal_' . $token, true);
                return redirect()->to('familia/' . $token . '/arquivos');
            }

            return redirect()->back()->with('error', 'Senha invalida.');
        }

        return view('family/login', ['token' => $token]);
    }

    public function files(string $token)
    {
        if (! session('family_portal_' . $token)) {
            return redirect()->to('familia/' . $token);
        }

        $access = (new FamilyPortalAccessModel())->where('token', $token)->where('active', 1)->first();
        if (! $access) {
            return redirect()->to('/login')->with('error', 'Acesso familiar nao encontrado.');
        }

        $allowed = db_connect()->table('family_portal_files')
            ->where('portal_access_id', $access['id'])
            ->get()
            ->getResultArray();

        $contractModel = new \App\Models\ContractModel();
        $documentModel = new \App\Models\DocumentModel();
        $settings = new AppSettingModel();

        $contractIds = array_column(array_filter($allowed, static fn ($item) => $item['file_type'] === 'contract'), 'file_id');
        $documentIds = array_column(array_filter($allowed, static fn ($item) => $item['file_type'] === 'document'), 'file_id');

        $showContracts = $settings->value('family_portal_show_contracts', '1') === '1';
        $contracts = [];
        if ($showContracts) {
            $contracts = $contractIds
                ? $contractModel->where('treatment_id', $access['treatment_id'])->whereIn('id', $contractIds)->findAll()
                : $contractModel->where('treatment_id', $access['treatment_id'])->findAll();
        }

        $documents = $documentIds
            ? $documentModel->where('treatment_id', $access['treatment_id'])->whereIn('id', $documentIds)->findAll()
            : $documentModel->where('treatment_id', $access['treatment_id'])->findAll();

        return view('family/files', [
            'contracts' => $contracts,
            'documents' => $documents,
            'scheduleItems' => $settings->value('family_portal_show_schedule', '1') === '1'
                ? (new ScheduleItemModel())->where('active', 1)->orderBy('sort_order', 'ASC')->orderBy('starts_at', 'ASC')->findAll()
                : [],
            'showQuestions' => $settings->value('family_portal_show_questions', '1') === '1',
            'questionsText' => $settings->value('family_portal_questions_text', ''),
            'whatsappGroupLink' => $settings->value('family_portal_whatsapp_group_link', ''),
            'token' => $token,
        ]);
    }

    public function contractPdf(string $token, int $id)
    {
        $access = $this->authorizedAccess($token);
        if (! $access) {
            return redirect()->to('familia/' . $token);
        }

        $contract = (new \App\Models\ContractModel())->where('id', $id)->where('treatment_id', $access['treatment_id'])->first();
        if (! $contract) {
            return redirect()->to('familia/' . $token . '/arquivos')->with('error', 'Contrato nao encontrado.');
        }

        $html = view('pdf/document', ['title' => $contract['title'], 'body' => $contract['body_snapshot']]);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="contrato.pdf"')
            ->setBody((new \App\Libraries\PdfService())->stream($html, 'contrato.pdf'));
    }

    public function documentPdf(string $token, int $id)
    {
        $access = $this->authorizedAccess($token);
        if (! $access) {
            return redirect()->to('familia/' . $token);
        }

        $document = (new \App\Models\DocumentModel())->where('id', $id)->where('treatment_id', $access['treatment_id'])->first();
        if (! $document) {
            return redirect()->to('familia/' . $token . '/arquivos')->with('error', 'Documento nao encontrado.');
        }

        $html = view('pdf/document', ['title' => $document['name'], 'body' => $document['body_snapshot']]);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . url_title($document['name'], '-', true) . '.pdf"')
            ->setBody((new \App\Libraries\PdfService())->stream($html, $document['name'] . '.pdf'));
    }

    private function authorizedAccess(string $token): ?array
    {
        if (! session('family_portal_' . $token)) {
            return null;
        }

        return (new FamilyPortalAccessModel())->where('token', $token)->where('active', 1)->first();
    }
}
