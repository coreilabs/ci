<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class NotificationsController extends BaseController
{
    public function pending()
    {
        $rows = db_connect()->table('user_notifications un')
            ->select('un.id AS user_notification_id, notifications.*')
            ->join('notifications', 'notifications.id = un.notification_id')
            ->where('un.user_id', session('user.id'))
            ->where('un.read_at', null)
            ->orderBy('notifications.created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return $this->response->setJSON($rows);
    }

    public function markRead(int $id)
    {
        db_connect()->table('user_notifications')
            ->where('id', $id)
            ->where('user_id', session('user.id'))
            ->update(['read_at' => date('Y-m-d H:i:s')]);

        return $this->response->setJSON(['ok' => true]);
    }

    public function callCoordinator(int $treatmentId)
    {
        $treatment = (new \App\Models\TreatmentModel())->listWithPeople()->where('treatments.id', $treatmentId)->first();
        if (! $treatment) {
            return redirect()->back()->with('error', 'Acolhido nao encontrado.');
        }

        (new NotificationModel())->createForRoleSlug('coordenador', [
            'type' => 'attendance_call',
            'title' => 'Chamada para atendimento',
            'body' => session('user.name') . ' chamou o acolhido ' . $treatment['patient_name'] . ' para atendimento.',
            'treatment_id' => $treatmentId,
        ]);

        (new \App\Models\AuditLogModel())->write($treatmentId, 'coordinator.called', [
            'requested_by' => session('user.id'),
        ]);

        return redirect()->back()->with('success', 'Coordenadores notificados.');
    }
}
