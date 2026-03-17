<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.whatsapp.url');
    }

    // Kirim WA ke banyak nomor
    public function sendWhatsapp(array $phones, string $message): array
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

    // Kirim Discord ke channel
    public function sendDiscord(string $channelId, string $message): bool
    {
        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}/discord/send", [
                'channel_id' => $channelId,
                'message'    => $message,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Discord send error: ' . $e->getMessage());
            return false;
        }
    }

    // Cek status koneksi
    public function isConnected(): array
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/status");
            return $response->json();
        } catch (\Exception $e) {
            return ['whatsapp' => false, 'discord' => false];
        }
    }
    public function getDiscordChannelName(string $channelId): string
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/discord/channel/{$channelId}");
            return $response->json('name') ?? $channelId;
        } catch (\Exception $e) {
            return $channelId;
        }
    }
    public function getTelegramChatName(string $chatId): string
    {
        try {
            $token    = config('services.telegram.token');
            $response = Http::timeout(5)->get("https://api.telegram.org/bot{$token}/getChat", [
                'chat_id' => $chatId,
            ]);

            return $response->json('result.title') ?? $chatId;
        } catch (\Exception $e) {
            return $chatId;
        }
    }
    public function sendTelegram(string $chatId, string $message): bool
    {
        try {
            $token    = config('services.telegram.token');
            $response = Http::timeout(10)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id'  => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram send error: ' . $e->getMessage());
            return false;
        }
    }
}
