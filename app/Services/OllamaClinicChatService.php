<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class OllamaClinicChatService
{
    public function reply(string $message, array $history = [], string $lang = 'ar'): string
    {
        $normalizedLang = $lang === 'ar' ? 'ar' : 'en';
        $urgentPrefix = $normalizedLang === 'ar'
            ? "🚨 توجه للطوارئ فورًا أو اتصل بالإسعاف الآن.\n\n"
            : "🚨 Go to emergency now or call emergency services immediately.\n\n";

        $trimmedMessage = trim($message);
        if ($trimmedMessage === '') {
            return $this->emptyMessageReply($normalizedLang);
        }

        if ($this->isSmallTalkMessage($trimmedMessage)) {
            return $this->smallTalkReply($normalizedLang);
        }

        $preparedHistory = $this->prepareHistory($history);
        $emergencyContext = $this->buildEmergencyContext($trimmedMessage, $preparedHistory);

        if ($this->isEmergencyText($emergencyContext)) {
            return $urgentPrefix.$this->buildEmergencyGuidance($normalizedLang);
        }

        if ($this->isMedicationOrDefinitiveTreatmentRequest($trimmedMessage)) {
            return $this->buildMedicationRefusal($normalizedLang);
        }

        if ($this->isDefinitiveDiagnosisRequest($trimmedMessage)) {
            return $this->buildDiagnosisRefusal($normalizedLang);
        }

        $messages = [
            [
                'role' => 'system',
                'content' => $this->systemPrompt($normalizedLang),
            ],
        ];

        foreach ($preparedHistory as $item) {
            $messages[] = $item;
        }

        $messages[] = [
            'role' => 'user',
            'content' => $trimmedMessage,
        ];

        try {
            $response = Http::baseUrl(rtrim((string) config('services.ollama.base_url'), '/'))
                ->timeout((int) config('services.ollama.timeout', 40))
                ->acceptJson()
                ->post('/api/chat', [
                    'model' => config('services.ollama.clinic_model', config('services.ollama.model', 'llama3.1:8b')),
                    'stream' => false,
                    'messages' => $messages,
                    'options' => [
                        'temperature' => 0.2,
                    ],
                ])
                ->throw();

            $content = trim((string) data_get($response->json(), 'message.content', ''));
            if ($content === '') {
                return $this->offlineTriageReply($trimmedMessage, $normalizedLang);
            }

            if ($this->isEmergencyText($emergencyContext) && ! str_starts_with($content, '🚨')) {
                return $urgentPrefix.$content;
            }

            return $this->appendSafetyNote($content, $normalizedLang);
        } catch (Throwable $exception) {
            Log::warning('Ollama clinic chat failed', [
                'error' => $exception->getMessage(),
            ]);

            return $this->offlineTriageReply($trimmedMessage, $normalizedLang);
        }
    }

    private function prepareHistory(array $history): array
    {
        $recent = array_slice($history, -6);
        $prepared = [];

        foreach ($recent as $item) {
            if (! is_array($item)) {
                continue;
            }

            $role = (string) ($item['role'] ?? '');
            $content = trim((string) ($item['message'] ?? ''));

            if ($content === '' || ! in_array($role, ['user', 'assistant', 'system'], true)) {
                continue;
            }

            $prepared[] = [
                'role' => $role,
                'content' => $content,
            ];
        }

        return $prepared;
    }

    private function buildEmergencyContext(string $message, array $preparedHistory): string
    {
        $chunks = [];

        foreach ($preparedHistory as $item) {
            if (($item['role'] ?? '') !== 'user') {
                continue;
            }

            $content = trim((string) ($item['content'] ?? ''));
            if ($content !== '') {
                $chunks[] = $content;
            }
        }

        $chunks[] = $message;

        return implode(' ', $chunks);
    }

    private function systemPrompt(string $lang): string
    {
        if ($lang === 'ar') {
            return <<<'PROMPT'
أنت مساعد فرز طبي أولي لموقع عيادة، وتكتب بالعربية الواضحة.

قواعد إلزامية:
1) لا تقدم تشخيصًا نهائيًا أبدًا.
2) لا تقدم جرعات أدوية ولا خطة علاج نهائية.
3) لا تطلب من المريض تعديل دواء موصوف له.
4) اذكر احتمالات عامة فقط بصياغة غير مؤكدة.
5) اطرح أسئلة متابعة ذكية ومحددة لتحسين الفرز الطبي.
6) قيّم الخطورة وحدد بوضوح التصنيف الأنسب:
   - طوارئ الآن
   - زيارة اليوم
   - خلال 24 ساعة
   - حجز عادي
7) راقب red flags مثل:
   ألم صدر شديد، ضيق نفس شديد، ضعف مفاجئ بأحد الأطراف، اضطراب وعي، تشنجات، نزيف شديد، حساسية شديدة، قيء دم، براز أسود، جفاف شديد عند الأطفال وكبار السن.
8) إذا ظهرت red flags اجعل بداية الرد سطرًا عاجلًا واضحًا للتوجه للطوارئ فورًا.
9) اجعل الرد بصيغة منظمة دائمًا بهذه العناوين وبهذا الترتيب:
   - التقييم الأولي
   - الاحتمالات العامة (غير مؤكدة)
   - أسئلة مهمة
   - ماذا تفعل الآن
   - متى تذهب للطوارئ فورًا
10) اختم دائمًا حرفيًا بهذه العبارة:
هذه المعلومات لا تغني عن الكشف الطبي.
PROMPT;
        }

        return <<<'PROMPT'
You are a medical first-triage assistant for a clinic website.

Hard rules:
1) Never provide a definitive diagnosis.
2) Never provide medication dosages or a final treatment plan.
3) Never instruct changing prescribed medications.
4) Mention only non-confirmed general possibilities.
5) Ask smart follow-up triage questions.
6) Assign one urgency level clearly:
   - Emergency now
   - Same-day visit
   - Within 24 hours
   - Routine booking
7) Detect red flags (severe chest pain, severe shortness of breath, stroke signs, confusion, seizures, severe bleeding, anaphylaxis, vomiting blood, black stool, severe dehydration).
8) If red flags are present, start response with urgent emergency instruction.
9) Always format with these sections in order:
   - Initial assessment
   - General possibilities (not confirmed)
   - Important questions
   - What to do now
   - When to go to emergency immediately
10) End every response with this exact sentence:
These details do not replace an in-person medical evaluation.
PROMPT;
    }

        private function isEmergencyText(string $message): bool
    {
        $latin = [
            'severe chest pain',
            'severe shortness of breath',
            'stroke',
            'seizure',
            'severe bleeding',
            'anaphylaxis',
            'vomiting blood',
            'black stool',
            'fainting',
            'loss of consciousness',
        ];

        $arabic = json_decode('["\u0623\u0644\u0645 \u0635\u062f\u0631 \u0634\u062f\u064a\u062f","\u0636\u064a\u0642 \u0646\u0641\u0633 \u0634\u062f\u064a\u062f","\u0636\u064a\u0642 \u062a\u0646\u0641\u0633 \u0634\u062f\u064a\u062f","\u0625\u063a\u0645\u0627\u0621","\u0641\u0642\u062f\u0627\u0646 \u0648\u0639\u064a","\u062a\u0634\u0646\u062c","\u0646\u0632\u064a\u0641 \u0634\u062f\u064a\u062f","\u0636\u0639\u0641 \u0645\u0641\u0627\u062c\u0626","\u062e\u062f\u0631 \u0645\u0641\u0627\u062c\u0626","\u0642\u064a\u0621 \u062f\u0645","\u0628\u0631\u0627\u0632 \u0623\u0633\u0648\u062f","\u062d\u0633\u0627\u0633\u064a\u0629 \u0634\u062f\u064a\u062f\u0629"]', true) ?: [];

        return $this->containsAny($message, $latin) || $this->containsAny($message, $arabic);
    }

    private function isDefinitiveDiagnosisRequest(string $message): bool
    {
        $latin = [
            'definitive diagnosis',
            'confirm diagnosis',
            'exact diagnosis',
            'what is my diagnosis',
        ];

        $arabic = json_decode('["\u0634\u062e\u0635\u0646\u064a","\u0634\u062e\u0651\u0635\u0646\u064a","\u0623\u0643\u062f \u0627\u0644\u062a\u0634\u062e\u064a\u0635","\u062a\u0623\u0643\u064a\u062f \u0627\u0644\u062a\u0634\u062e\u064a\u0635","\u0642\u0644 \u0644\u064a \u0627\u0644\u062a\u0634\u062e\u064a\u0635"]', true) ?: [];

        return $this->containsAny($message, $latin) || $this->containsAny($message, $arabic);
    }

    private function isMedicationOrDefinitiveTreatmentRequest(string $message): bool
    {
        $latin = [
            'dosage',
            'dose',
            'prescription',
            'change my medication',
            'stop my medication',
            'final treatment',
        ];

        $arabic = json_decode('["\u062c\u0631\u0639\u0629","\u0643\u0645 \u0645\u0644\u063a","\u0643\u0645 \u0645\u0631\u0629","\u0627\u0643\u062a\u0628 \u0644\u064a \u062f\u0648\u0627\u0621","\u0648\u0635\u0641\u0629","\u063a\u064a\u0631 \u062f\u0648\u0627\u0626\u064a","\u063a\u064a\u0651\u0631 \u062f\u0648\u0627\u0626\u064a","\u0648\u0642\u0641 \u062f\u0648\u0627\u0626\u064a","\u0639\u0644\u0627\u062c \u0646\u0647\u0627\u0626\u064a"]', true) ?: [];

        return $this->containsAny($message, $latin) || $this->containsAny($message, $arabic);
    }

    private function containsAny(string $message, array $needles): bool
    {
        $normalized = mb_strtolower($message, 'UTF-8');

        foreach ($needles as $needle) {
            $word = mb_strtolower((string) $needle, 'UTF-8');
            if ($word !== '' && mb_strpos($normalized, $word, 0, 'UTF-8') !== false) {
                return true;
            }
        }

        return false;
    }

    private function isSmallTalkMessage(string $message): bool
    {
        $latin = ['hi', 'hello', 'hey', 'good morning', 'good evening'];
        $arabic = json_decode('["\u0645\u0631\u062d\u0628\u0627","\u0627\u0647\u0644\u0627","\u0623\u0647\u0644\u0627","\u0633\u0644\u0627\u0645","\u0627\u0644\u0633\u0644\u0627\u0645 \u0639\u0644\u064a\u0643\u0645","\u0639\u0644\u064a\u0643\u0645 \u0627\u0644\u0633\u0644\u0627\u0645","\u0647\u0627\u064a","\u0647\u0644\u0627"]', true) ?: [];

        return $this->containsAny($message, $latin) || $this->containsAny($message, $arabic);
    }

    private function smallTalkReply(string $lang): string
    {
        if ($lang === 'ar') {
            return "مرحبًا، أنا مساعد فرز طبي أولي. من فضلك اكتب الأعراض الأساسية، مدتها، والعمر لبدء التقييم الأولي.\n\nهذه المعلومات لا تغني عن الكشف الطبي.";
        }

        return "Hello, I am a first-triage medical assistant. Please share the main symptoms, duration, and age to start an initial triage.\n\nThese details do not replace an in-person medical evaluation.";
    }

    private function buildEmergencyGuidance(string $lang): string
    {
        if ($lang === 'ar') {
            return $this->appendSafetyNote(<<<'TXT'
التقييم الأولي:
- توجد أعراض إنذار قد تكون خطيرة وتحتاج تقييمًا عاجلًا.

الاحتمالات العامة (غير مؤكدة):
- قد تكون الحالة قلبية أو تنفسية أو عصبية أو حساسية شديدة، ولا يمكن تأكيد السبب عبر الدردشة.

أسئلة مهمة:
- متى بدأت الأعراض؟ وهل تسوء بسرعة؟
- هل يوجد إغماء، ازرقاق، ضعف مفاجئ، أو ألم شديد مستمر؟

ماذا تفعل الآن:
- لا تؤخر طلب المساعدة الطبية.
- توجّه للطوارئ فورًا أو اتصل بالإسعاف.

متى تذهب للطوارئ فورًا:
- الآن دون انتظار.
TXT, 'ar');
        }

        return $this->appendSafetyNote(<<<'TXT'
Initial assessment:
- Alarm symptoms are present and need urgent in-person evaluation.

General possibilities (not confirmed):
- Causes could be cardiac, respiratory, neurologic, or severe allergic reactions; this cannot be confirmed in chat.

Important questions:
- When did symptoms start and are they rapidly worsening?
- Any fainting, cyanosis, severe persistent pain, or focal weakness?

What to do now:
- Do not delay medical care.
- Go to emergency now or call emergency services.

When to go to emergency immediately:
- Right now without waiting.
TXT, 'en');
    }

    private function buildDiagnosisRefusal(string $lang): string
    {
        if ($lang === 'ar') {
            return $this->appendSafetyNote(<<<'TXT'
التقييم الأولي:
- لا يمكنني تقديم تشخيص مؤكد عبر الدردشة.

الاحتمالات العامة (غير مؤكدة):
- يمكن فقط ذكر احتمالات مبدئية بعد معرفة الأعراض الأساسية.

أسئلة مهمة:
- ما الأعراض الرئيسية؟
- منذ متى بدأت؟
- هل توجد حرارة، ألم شديد، ضيق نفس، أو أعراض عصبية؟

ماذا تفعل الآن:
- الأفضل إجراء كشف سريري لدى طبيب لتأكيد السبب.

متى تذهب للطوارئ فورًا:
- إذا ظهر ألم صدر شديد، ضيق نفس شديد، إغماء، نزيف شديد، أو ضعف مفاجئ.
TXT, 'ar');
        }

        return $this->appendSafetyNote(<<<'TXT'
Initial assessment:
- I cannot provide a confirmed diagnosis through chat.

General possibilities (not confirmed):
- Only preliminary possibilities can be discussed after symptom review.

Important questions:
- What are the main symptoms?
- When did they start?
- Any fever, severe pain, breathing difficulty, or neurologic symptoms?

What to do now:
- Book an in-person medical evaluation for confirmation.

When to go to emergency immediately:
- Severe chest pain, severe shortness of breath, fainting, severe bleeding, or sudden weakness.
TXT, 'en');
    }

    private function buildMedicationRefusal(string $lang): string
    {
        if ($lang === 'ar') {
            return $this->appendSafetyNote(<<<'TXT'
التقييم الأولي:
- لا أستطيع تقديم جرعات دوائية أو وصف علاج نهائي أو تعديل دواء موصوف عبر الدردشة.

الاحتمالات العامة (غير مؤكدة):
- يمكن تقديم إرشادات عامة فقط حسب الأعراض.

أسئلة مهمة:
- ما الأعراض الحالية؟
- هل لديك أمراض مزمنة أو أدوية ثابتة؟
- هل توجد حساسية دوائية؟

ماذا تفعل الآن:
- راجع طبيبك أو الصيدلي السريري قبل أي تعديل دوائي.
- إذا الأعراض شديدة، اطلب تقييمًا عاجلًا.

متى تذهب للطوارئ فورًا:
- عند ضيق نفس شديد، تورم بالوجه/اللسان، طفح شديد مفاجئ، أو تدهور سريع.
TXT, 'ar');
        }

        return $this->appendSafetyNote(<<<'TXT'
Initial assessment:
- I cannot provide medication dosages, final treatment plans, or alter prescribed medications in chat.

General possibilities (not confirmed):
- I can only provide safe general guidance based on symptoms.

Important questions:
- What symptoms are present now?
- Do you have chronic illnesses or regular medications?
- Any drug allergies?

What to do now:
- Contact your clinician or pharmacist before changing any medication.
- Seek urgent care if symptoms are severe.

When to go to emergency immediately:
- Severe breathing trouble, facial/tongue swelling, severe sudden rash, or rapid deterioration.
TXT, 'en');
    }

    private function emptyMessageReply(string $lang): string
    {
        return $lang === 'ar'
            ? "من فضلك اكتب سؤالك الطبي بإيجاز (العمر، الأعراض، مدة الأعراض). هذه المعلومات لا تغني عن الكشف الطبي."
            : "Please write your medical concern briefly (age, symptoms, duration). These details do not replace an in-person medical evaluation.";
    }

    private function serviceUnavailableReply(string $lang): string
    {
        if ($lang === 'ar') {
            return $this->appendSafetyNote(
                "التقييم الأولي:\n- خدمة المساعد الطبي غير متاحة حاليًا.\n\nالاحتمالات العامة (غير مؤكدة):\n- لا يمكن تحليل الحالة الآن عبر النظام.\n\nأسئلة مهمة:\n- اذكر الأعراض الأساسية، مدتها، وشدة الألم.\n\nماذا تفعل الآن:\n- إذا الحالة غير مستقرة توجّه للطوارئ.\n- إن كانت مستقرة احجز كشفًا في أقرب وقت.\n\nمتى تذهب للطوارئ فورًا:\n- ألم صدر شديد، ضيق نفس شديد، إغماء، نزيف شديد، أو أعراض عصبية مفاجئة.",
                'ar'
            );
        }

        return $this->appendSafetyNote(
            "Initial assessment:\n- The medical assistant service is currently unavailable.\n\nGeneral possibilities (not confirmed):\n- The case cannot be analyzed by the system now.\n\nImportant questions:\n- Share core symptoms, duration, and severity.\n\nWhat to do now:\n- If unstable, go to emergency now.\n- If stable, book an in-person visit soon.\n\nWhen to go to emergency immediately:\n- Severe chest pain, severe shortness of breath, fainting, severe bleeding, or sudden neurologic symptoms.",
            'en'
        );
    }

    private function appendSafetyNote(string $content, string $lang): string
    {
        $note = $lang === 'ar'
            ? 'هذه المعلومات لا تغني عن الكشف الطبي.'
            : 'These details do not replace an in-person medical evaluation.';

        return str_contains($content, $note) ? trim($content) : trim($content)."\n\n".$note;
    }
    private function offlineTriageReply(string $message, string $lang): string
    {
        if ($this->isSmallTalkMessage($message)) {
            return $this->smallTalkReply($lang);
        }

        $severity = 'routine';

        if ($this->isEmergencyText($message)) {
            $severity = 'emergency';
        } elseif ((bool) preg_match('/(حرارة شديدة|ألم شديد|قيء متكرر|طفل|رضيع|كبير السن|دوخة شديدة|severe pain|high fever|persistent vomiting|infant|elderly|pregnant)/iu', $message)) {
            $severity = 'same_day';
        } elseif ((bool) preg_match('/(حمى|سعال|التهاب حلق|صداع|إسهال|قيء|rash|fever|cough|sore throat|headache|diarrhea|vomiting)/iu', $message)) {
            $severity = 'within_24h';
        }

        if ($lang === 'ar') {
            $urgency = match ($severity) {
                'emergency' => 'طوارئ الآن',
                'same_day' => 'زيارة اليوم',
                'within_24h' => 'خلال 24 ساعة',
                default => 'حجز عادي',
            };

            $start = $severity === 'emergency'
                ? "🚨 توجه للطوارئ فورًا أو اتصل بالإسعاف الآن.\n\n"
                : '';

            $reply = $start.
"التقييم الأولي:\n".
"- مستوى الخطورة المبدئي: {$urgency}.\n".
"- هذا فرز أولي مبني على الأعراض المكتوبة فقط.\n\n".
"الاحتمالات العامة (غير مؤكدة):\n".
"- قد تكون الحالة التهابًا/عدوى بسيطة أو سببًا آخر يحتاج فحصًا سريريًا.\n\n".
"أسئلة مهمة:\n".
"- منذ متى بدأت الأعراض؟ وهل تزداد بسرعة؟\n".
"- ما شدة الألم (من 10)؟ وهل توجد حرارة مقاسة؟\n".
"- هل يوجد ضيق نفس، ألم صدر، إغماء، نزيف، أو جفاف شديد؟\n\n".
"ماذا تفعل الآن:\n".
"- راقب الأعراض، احصل على سوائل كافية، وتجنب تغيير أي دواء موصوف دون طبيب.\n".
"- احجز زيارة حسب درجة الخطورة المذكورة أعلاه.\n\n".
"متى تذهب للطوارئ فورًا:\n".
"- ألم صدر شديد، ضيق نفس شديد، إغماء، نزيف شديد، ضعف مفاجئ بأحد الأطراف، تشنجات، أو تدهور سريع.";

            return $this->appendSafetyNote($reply, 'ar');
        }

        $urgency = match ($severity) {
            'emergency' => 'Emergency now',
            'same_day' => 'Same-day visit',
            'within_24h' => 'Within 24 hours',
            default => 'Routine booking',
        };

        $start = $severity === 'emergency'
            ? "🚨 Go to emergency now or call emergency services immediately.\n\n"
            : '';

        $reply = $start.
"Initial assessment:\n".
"- Preliminary urgency level: {$urgency}.\n".
"- This is first-triage guidance based only on written symptoms.\n\n".
"General possibilities (not confirmed):\n".
"- Possibilities include mild infection/inflammation or another condition requiring in-person assessment.\n\n".
"Important questions:\n".
"- When did symptoms start and are they worsening?\n".
"- Any measured temperature or pain score?\n".
"- Any breathing difficulty, chest pain, fainting, bleeding, or severe dehydration?\n\n".
"What to do now:\n".
"- Monitor symptoms, hydrate, and avoid changing medications without your clinician.\n".
"- Book care based on urgency above.\n\n".
"When to go to emergency immediately:\n".
"- Severe chest pain, severe shortness of breath, fainting, severe bleeding, one-sided weakness, seizures, or rapid deterioration.";

        return $this->appendSafetyNote($reply, 'en');
    }
}