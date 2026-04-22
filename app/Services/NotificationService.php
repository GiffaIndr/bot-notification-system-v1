<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected $baseUrl;
    protected $serviceKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            config('services.bot_service.url', config('services.whatsapp.url', 'http://localhost:3000')),
            '/'
        );
        $this->serviceKey = config('services.bot_service.key', env('BOT_SERVICE_API_KEY'));
    }

    protected function client(int $timeout = 30)
    {
        $client = Http::timeout($timeout);

        if (!empty($this->serviceKey)) {
            $client = $client->withHeaders([
                'X-Service-Key' => $this->serviceKey,
            ]);
        }

        return $client;
    }

    // Kirim WA ke banyak nomor
    public function sendWhatsapp(array $phones, string $message): array
    {
        try {
            $response = $this->client(30)->post("{$this->baseUrl}/send-bulk", [
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
            $response = $this->client(10)->post("{$this->baseUrl}/discord/send", [
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
            $response = $this->client(5)->get("{$this->baseUrl}/status");
            return $response->json();
        } catch (\Exception $e) {
            return ['whatsapp' => false, 'discord' => false, 'telegram' => false];
        }
    }
    public function getDiscordChannelName(string $channelId): string
    {
        try {
            $response = $this->client(5)->get("{$this->baseUrl}/discord/channel/{$channelId}");
            return $response->json('name') ?? $channelId;
        } catch (\Exception $e) {
            return $channelId;
        }
    }
    public function getTelegramChatName(string $chatId): string
    {
        try {
            $response = $this->client(5)->get("{$this->baseUrl}/telegram/chat/{$chatId}");

            $name = trim((string) ($response->json('name') ?? ''));

            if ($name === '' || $name === $chatId || preg_match('/^-?\d+$/', $name)) {
                return '';
            }

            return $name;
        } catch (\Exception $e) {
            return '';
        }
    }
    public function requestTelegramConnectLink(string $state): array
    {
        try {
            $response = $this->client(10)->get("{$this->baseUrl}/telegram/connect-link", [
                'state' => $state,
            ]);

            return [
                'status' => $response->status(),
                'ok' => $response->successful(),
                'data' => $response->json() ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('Telegram connect link error: ' . $e->getMessage());
            return [
                'status' => 500,
                'ok' => false,
                'data' => ['message' => $e->getMessage()],
            ];
        }
    }
    public function claimTelegramConnect(string $token): array
    {
        try {
            $response = $this->client(10)->post("{$this->baseUrl}/telegram/connect/claim", [
                'token' => $token,
            ]);

            return [
                'status' => $response->status(),
                'ok' => $response->successful(),
                'data' => $response->json() ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('Telegram claim connect error: ' . $e->getMessage());
            return [
                'status' => 500,
                'ok' => false,
                'data' => ['message' => $e->getMessage()],
            ];
        }
    }
    public function getDiscordInviteUrl(string $state): ?string
    {
        try {
            $response = $this->client(5)->get("{$this->baseUrl}/discord/invite-url", [
                'state' => $state,
            ]);

            return $response->json('url') ?? $response->json('invite_url');
        } catch (\Exception $e) {
            Log::error('Discord invite url error: ' . $e->getMessage());
            return null;
        }
    }
    public function getTelegramConnectLink(string $token): ?string
    {
        try {
            $response = $this->client(5)->get("{$this->baseUrl}/telegram/connect-link", [
                'token' => $token,
            ]);

            return $response->json('url') ?? $response->json('connect_url');
        } catch (\Exception $e) {
            Log::error('Telegram connect link error: ' . $e->getMessage());
            return null;
        }
    }
    public function sendWhatsappFile(array $phones, $attachment, string $caption = ''): void
    {
        try {
            $filePath = storage_path('app/public/' . $attachment->path);

            if (!file_exists($filePath)) {
                Log::error('File not found: ' . $filePath);
                return;
            }

            $fileData = base64_encode(file_get_contents($filePath));

            $this->client(30)->post("{$this->baseUrl}/send-file", [
                'phones'    => $phones,
                'filename'  => $attachment->filename,
                'type'      => $attachment->type,
                'mime_type' => $attachment->mime_type,
                'data'      => $fileData,
                'caption'   => $caption,
            ]);
        } catch (\Exception $e) {
            Log::error('WhatsApp file send error: ' . $e->getMessage());
        }
    }
    public function sendDiscordWithFiles(string $channelId, string $message, $attachments): void
    {
        try {
            $files = [];
            foreach ($attachments as $attachment) {
                $filePath = storage_path('app/public/' . $attachment->path);
                if (file_exists($filePath)) {
                    $fileData = base64_encode(file_get_contents($filePath));
                    $files[]  = [
                        'filename'  => $attachment->filename,
                        'mime_type' => $attachment->mime_type,
                        'data'      => $fileData,
                    ];
                }
            }

            $this->client(30)->post("{$this->baseUrl}/discord/send-with-files", [
                'channel_id' => $channelId,
                'message'    => $message,
                'files'      => $files,
            ]);
        } catch (\Exception $e) {
            Log::error('Discord send with files error: ' . $e->getMessage());
        }
    }

    public function sendDiscordFile(string $channelId, $attachment): void
    {
        try {
            $filePath = storage_path('app/public/' . $attachment->path);

            if (!file_exists($filePath)) {
                Log::error('File not found: ' . $filePath);
                return;
            }

            $fileData = base64_encode(file_get_contents($filePath));

            $this->client(30)->post("{$this->baseUrl}/discord/send-file", [
                'channel_id' => $channelId,
                'filename'   => $attachment->filename,
                'mime_type'  => $attachment->mime_type,
                'data'       => $fileData,
            ]);
        } catch (\Exception $e) {
            Log::error('Discord file send error: ' . $e->getMessage());
        }
    }

    public function sendTelegramFile(string $chatId, $attachment, string $caption = ''): void
    {
        try {
            $filePath = storage_path('app/public/' . $attachment->path);

            if (!file_exists($filePath)) {
                Log::error('File not found: ' . $filePath);
                return;
            }

            $fileData = base64_encode(file_get_contents($filePath));

            $this->client(30)->post("{$this->baseUrl}/telegram/send-file", [
                'chat_id'    => $chatId,
                'filename'   => $attachment->filename,
                'mime_type'  => $attachment->mime_type,
                'type'       => $attachment->type,
                'data'       => $fileData,
                'caption'    => $caption ?: $attachment->filename,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram file send error: ' . $e->getMessage());
        }
    }
    public function sendTelegram(string $chatId, string $message): array
    {
        try {
            $response = $this->client(20)->post("{$this->baseUrl}/telegram/send", [
                'chat_id' => $chatId,
                'message' => $message,
                'parse_mode' => 'Markdown',
            ]);

            $body = $response->json() ?? [];
            $nodeSuccess = (bool) ($body['success'] ?? false);
            $ok = $response->status() === 200 && $nodeSuccess;

            return [
                'ok' => $ok,
                'status' => $response->status(),
                'data' => $body,
            ];
        } catch (\Exception $e) {
            Log::error('Telegram send error: ' . $e->getMessage());

            return [
                'ok' => false,
                'status' => 500,
                'data' => ['message' => $e->getMessage()],
            ];
        }
    }
}
