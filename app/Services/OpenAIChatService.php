<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class OpenAIChatService
{
    public function reply(string $message, string $lang = 'en'): string
    {
        $disclaimer = $this->disclaimer($lang);
        $apiKey = config('services.openai.api_key');

        if (! $apiKey) {
            return $this->appendDisclaimer(
                $lang === 'ar'
                    ? 'لا يوجد إعداد صحيح لخدمة الذكاء حالياً. اكتب الأعراض بشكل مختصر وسأعطيك إرشاداً عاماً.'
                    : 'I can share general information only. Describe the symptoms briefly and I will provide general guidance.',
                $disclaimer
            );
        }

        $baseUrl = rtrim((string) config('services.openai.base_url'), '/');
        $timeout = (int) config('services.openai.timeout', 30);
        $preferredModel = (string) config('services.openai.model', 'gpt-4o-mini');
        $modelsToTry = array_values(array_unique([$preferredModel, 'gpt-4o-mini']));
        $lastErrorMessage = '';

        foreach ($modelsToTry as $model) {
            try {
                $response = Http::baseUrl($baseUrl)
                    ->timeout($timeout)
                    ->acceptJson()
                    ->withToken($apiKey)
                    ->post('/chat/completions', [
                        'model' => $model,
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => $this->systemPrompt($lang),
                            ],
                            [
                                'role' => 'user',
                                'content' => $message,
                            ],
                        ],
                    ])
                    ->throw();

                $content = trim((string) data_get($response->json(), 'choices.0.message.content', ''));

                return $this->appendDisclaimer($content, $disclaimer);
            } catch (Throwable $exception) {
                $status = $exception instanceof RequestException ? $exception->response?->status() : null;
                $body = $exception instanceof RequestException
                    ? ($exception->response?->json() ?? $exception->response?->body())
                    : $exception->getMessage();
                $lastErrorMessage = (string) data_get($body, 'error.message', '');

                Log::warning('OpenAI request failed', [
                    'model' => $model,
                    'status' => $status,
                    'error' => $lastErrorMessage ?: $body,
                ]);

                if (! in_array($status, [400, 404], true)) {
                    break;
                }
            }
        }

        $fallbackMessage = $lang === 'ar'
            ? 'خدمة الذكاء الاصطناعي غير متاحة حالياً. جرّب مرة أخرى لاحقاً أو احجز موعداً مع الطبيب.'
            : 'The AI service is temporarily unavailable. Please try again later or book an appointment with a doctor.';

        return $this->appendDisclaimer($fallbackMessage, $disclaimer);
    }

    private function systemPrompt(string $lang): string
    {
        if ($lang === 'ar') {
            return 'أنت مساعد طبي للعيادة. قدم إرشاداً عاماً فقط، لا تقدم تشخيصاً نهائياً، لا تعطِ تعليمات دوائية خطرة، وانصح بزيارة طبيب عند الأعراض الشديدة. اختم دائماً بهذه العبارة: '.$this->disclaimer('ar');
        }

        return 'You are a clinic medical assistant. Provide general advice only, never a definitive diagnosis, avoid risky medication instructions, and advise seeing a doctor for severe symptoms. Always end with: '.$this->disclaimer('en');
    }

    private function disclaimer(string $lang): string
    {
        return $lang === 'ar'
            ? 'هذه ليست تشخيصاً طبياً. يرجى مراجعة الطبيب.'
            : 'This is not a medical diagnosis. Please consult a doctor.';
    }

    private function appendDisclaimer(string $content, string $disclaimer): string
    {
        return str_contains($content, $disclaimer)
            ? trim($content)
            : trim($content).' '.$disclaimer;
    }
}
