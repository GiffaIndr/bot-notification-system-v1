<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.whatsapp.url');
    }

    // Kirim ke 1 nomor
    public function send(string $phone, string $message): bool
    {
        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/send", [
                'phone'   => $phone,
                'message' => $message,
            ]);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('WhatsApp send error: ' . $e->getMessage());
            return false;
        }
    }

    // Kirim ke banyak nomor sekaligus
    public function sendBulk(array $phones, string $message): array
    {
        try {
            $response = Http::timeout(30)->post("{$this->baseUrl}/send-bulk", [
                'phones'  => $phones,
                'message' => $message,
            ]);

            return $response->json();

        } catch (\Exception $e) {
            Log::error('WhatsApp bulk send error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // Cek status koneksi
    public function isConnected(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/status");
            return $response->json('connected') === true;

        } catch (\Exception $e) {
            return false;
        }
    }
}
