<?php

namespace App\Libraries;

use App\Models\AppSettingModel;

class WhatsAppService
{
    private AppSettingModel $settings;

    public function __construct()
    {
        $this->settings = new AppSettingModel();
    }

    public function sendText(string $to, string $message): array
    {
        $enabled = $this->settings->value('whatsapp_enabled', '0') === '1';
        $token = trim((string) $this->settings->value('whatsapp_access_token', ''));
        $phoneNumberId = trim((string) $this->settings->value('whatsapp_phone_number_id', ''));
        $version = trim((string) $this->settings->value('whatsapp_graph_version', 'v20.0'));

        if (! $enabled || $token === '' || $phoneNumberId === '') {
            return ['ok' => false, 'response' => 'WhatsApp nao configurado.'];
        }

        $to = preg_replace('/\D+/', '', $to);
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $message,
            ],
        ];

        $ch = curl_init('https://graph.facebook.com/' . $version . '/' . $phoneNumberId . '/messages');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'ok' => $status >= 200 && $status < 300,
            'status' => $status,
            'response' => $response ?: $error,
        ];
    }
}
