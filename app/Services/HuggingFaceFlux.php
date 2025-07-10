<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HuggingFaceFlux
{
    protected $apiUrl = 'https://black-forest-labs-flux-1-dev.hf.space/gradio_api/call/infer';
    protected $timeout = 50;
    protected $pollingTimeout = 500;
    protected $maxPollingAttempts = 10;

    public function generateImage(string $prompt): array
    {
        $sanitizedPrompt = $this->sanitizePrompt($prompt);

        try {
            // 1. Initiate generation
            $initResponse = Http::timeout($this->timeout)
                ->post($this->apiUrl, [
                    'data' => [
                        $sanitizedPrompt,
                        0,
                        true,
                        512,
                        512,
                        7,
                        50
                    ]
                ]);

            if (!$initResponse->successful()) {
                throw new \Exception("API request failed: HTTP " . $initResponse->status());
            }

            $initData = $initResponse->json();

            if (empty($initData['event_id'])) {
                throw new \Exception("API response missing event ID");
            }

            $imageData = $this->getGeneratedImage($initData['event_id']);

            if (!$imageData) {
                throw new \Exception("Image generation timed out");
            }

            return [
                'success' => true,
                'image_url' => $imageData['url'],
                'mime_type' => $imageData['mime_type']
            ];

        } catch (\Exception $e) {
            Log::error('Image generation failed', [
                'error' => $e->getMessage(),
                'prompt' => $sanitizedPrompt
            ]);

            return [
                'success' => false,
                'message' => $this->getUserMessage($e),
                'retry_suggestion' => $this->getRetrySuggestion($e)
            ];
        }
    }

    protected function getGeneratedImage(string $eventId): ?array
    {
        $pollUrl = $this->apiUrl . '/' . $eventId;

        for ($attempt = 1; $attempt <= $this->maxPollingAttempts; $attempt++) {
            try {
                $response = Http::timeout($this->pollingTimeout)
                    ->get($pollUrl);

                $data = $response->json();

                if (!empty($data['data'][0])) {
                    return [
                        'url' => $data['data'][0],
                        'mime_type' => $this->extractMimeType($data['data'][0])
                    ];
                }

                if (isset($data['error'])) {
                    throw new \Exception("API reported error: " . $data['error']);
                }

                sleep(5);

            } catch (\Exception $e) {
                Log::warning("Polling attempt {$attempt} failed: " . $e->getMessage());
                sleep(5);
            }
        }

        return null;
    }

    protected function getUserMessage(\Exception $e): string
    {
        $message = $e->getMessage();

        if (str_contains($message, 'timed out')) {
            return 'The image took too long to generate.';
        }

        if (str_contains($message, 'event_id')) {
            return 'Failed to start image generation.';
        }

        if (str_contains($message, 'HTTP 5')) {
            return 'The image service is currently unavailable.';
        }

        return 'Image generation failed.';
    }

    protected function getRetrySuggestion(\Exception $e): string
    {
        $message = $e->getMessage();

        if (str_contains($message, 'timed out')) {
            return 'Try a simpler prompt or wait a few minutes before trying again.';
        }

        return 'Please try again with different wording.';
    }

    protected function sanitizePrompt(string $prompt): string
    {
        return trim(substr(preg_replace('/[^\w\s\-.,!?\']/', '', strip_tags($prompt)), 0, 500));
    }

    protected function extractMimeType(string $base64Data): string
    {
        return preg_match('/^data:(image\/\w+);base64/', $base64Data, $matches)
            ? $matches[1]
            : 'image/png';
    }
}