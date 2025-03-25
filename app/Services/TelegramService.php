<?php

namespace App\Services;

use GuzzleHttp\Client;

class TelegramService
{
    public function sendTelegramMessage($message)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        $client = new Client();

        try {
            $response = $client->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'form_params' => [
                    'chat_id' => $chatId,
                    'text' => $message,
                ]
            ]);

            $body = $response->getBody();
            $data = json_decode($body, true);

            // Log the response to check what Telegram returns
            \Log::info('Telegram Response:', $data);

            if ($data['ok']) {
                return response()->json(['message' => 'Message sent successfully']);
            } else {
                return response()->json(['error' => 'Failed to send message', 'details' => $data]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
}

// composer require guzzlehttp/guzzle (CMD FR DEPNDC)


