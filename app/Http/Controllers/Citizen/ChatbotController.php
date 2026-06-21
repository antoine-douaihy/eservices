<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function sendMessage(Request $request)
    {
        // Validate the incoming text
        $request->validate(['message' => 'required|string|max:1000']);

        $userMessage = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json(['reply' => 'Missing API configuration.'], 500);
        }

        $locale = app()->getLocale(); // set by the SetLocale middleware — 'en' or 'ar'

        $languageInstruction = $locale === 'ar'
            ? 'Always reply in simple Modern Standard Arabic, unless the citizen writes clearly in English — then reply in English instead.'
            : 'Always reply in English, unless the citizen writes clearly in Arabic — then reply in Arabic instead.';

        $systemPrompt = 'You are the helpful, patient virtual assistant for "E-Services Platform", a Lebanese municipal '
            . 'e-government website. Your job is to help citizens use the website: browsing services, applying, '
            . "uploading documents, paying fees, tracking requests, booking appointments, and downloading certificates "
            . "or receipts. {$languageInstruction} Keep answers short, simple, and friendly — many users are elderly "
            . "and not very tech-savvy, so avoid jargon and give clear step-by-step instructions when relevant. Only "
            . "answer questions about this website and Lebanese municipal services; if asked something unrelated, "
            . "politely redirect to how you can help with the platform.\n\n"
            . "Here is factual information about how the website works and the services it currently offers. Use it "
            . "to answer accurately, and do not invent services, prices, or documents that aren't listed below:\n\n"
            . $this->buildKnowledgeBase($locale);

        // Updated to use the 2.5 Flash model instead of the retired 1.5 model
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        $fallback = $locale === 'ar'
            ? 'عذراً، لا يمكنني الاتصال بالمساعد الذكي الآن. يرجى المحاولة بعد قليل، أو الاستمرار في استخدام الموقع مباشرة.'
            : "Sorry, I can't reach the assistant right now. Please try again in a moment, or continue using the site directly.";

        try {
            $response = Http::timeout(15)
                ->connectTimeout(5)
                ->retry(1, 300)
                ->post($url, [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $systemPrompt . "\n\nCitizen's question: " . $userMessage],
                            ],
                        ],
                    ],
                ]);

            if ($response->successful()) {
                $reply = $response->json('candidates.0.content.parts.0.text') ?? $fallback;

                // Strip out markdown bolding for plain text UI
                $reply = preg_replace('/\*\*(.*?)\*\*/', '$1', $reply);

                return response()->json(['reply' => $reply]);
            }

            // Log the real Google API error for debugging, but never show
            // raw API/network internals to the citizen.
            Log::warning('Chatbot API error', ['status' => $response->status(), 'body' => $response->body()]);
            return response()->json(['reply' => $fallback], 500);

        } catch (\Exception $e) {
            // Almost always a local network/DNS issue (e.g. the server
            // hosting this app can't resolve generativelanguage.googleapis.com).
            // Log it for diagnosis, but keep the citizen-facing message clean.
            Log::error('Chatbot connection error: ' . $e->getMessage());
            return response()->json(['reply' => $fallback], 500);
        }
    }

    /**
     * Builds a compact, factual description of the platform's navigation,
     * application flow, and currently live services (with real prices and
     * required documents) so the assistant answers from real data instead
     * of guessing. Cached briefly per locale since service data rarely
     * changes minute-to-minute, and this avoids re-querying on every
     * single chat message.
     */
    private function buildKnowledgeBase(string $locale): string
    {
        return Cache::remember("chatbot.knowledge.{$locale}", 600, function () use ($locale) {
            $isAr = $locale === 'ar';

            $overview = $isAr
                ? "نظرة عامة على المنصة:\n"
                    . "- يمكن للمواطن إنشاء حساب أو تسجيل الدخول، ثم تصفح الخدمات من صفحة \"تصفح الخدمات\".\n"
                    . "- كل خدمة متوفرة في كل البلديات (المكاتب) المسجّلة على المنصة، فلا حاجة للبحث عن بلدية معينة.\n"
                    . "- لتقديم طلب: يختار المواطن الخدمة، ثم يكمل 3 خطوات: (1) بياناته الشخصية واختيار أقرب مكتب يدويًا أو عبر تحديد الموقع، (2) تحميل المستندات المطلوبة، (3) مراجعة الطلب وإرساله.\n"
                    . "- إذا كانت الخدمة مدفوعة، يدفع المواطن عبر بطاقة (Stripe) أو عملة مشفرة (Bitcoin أو Ethereum) من صفحة الدفع.\n"
                    . "- يمكن تتبّع الطلبات من صفحتي \"طلباتي\" و \"تطبيقاتي\"، أو عبر رمز QR خاص بكل طلب يفتح صفحة تتبع عامة بدون تسجيل دخول.\n"
                    . "- إذا حدد المكتب موعدًا حضوريًا، يظهر في \"مواعيدي\" ويمكن تأكيده أو إلغاؤه من هناك.\n"
                    . "- بعد الموافقة على الطلب، يمكن تحميل الشهادة أو إيصال الدفع من صفحة الطلب نفسها.\n"
                    . "- يمكن محادثة موظف المكتب مباشرة من زر \"محادثة\" داخل صفحة كل طلب.\n"
                    . "- يمكن تبديل اللغة بين العربية والإنجليزية من الزر الموجود في الأعلى.\n\n"
                : "Platform overview:\n"
                    . "- Citizens create an account or log in, then browse services from \"Browse Services\".\n"
                    . "- Every service is available at every registered municipality office, so there's no need to find a specific municipality first.\n"
                    . "- To apply: pick a service, then complete 3 steps — (1) personal details and choosing the nearest office, either manually or via location detection, (2) uploading the required documents, (3) reviewing and submitting.\n"
                    . "- If a service has a fee, payment is made by card (Stripe) or cryptocurrency (Bitcoin or Ethereum) on the Payment page.\n"
                    . "- Requests can be tracked from \"My Requests\" and \"My Applications\", or via a QR code given per request that opens a public tracking page without needing to log in.\n"
                    . "- If the office schedules an in-person appointment, it appears under \"My Appointments\" where it can be confirmed or cancelled.\n"
                    . "- Once a request is approved, the certificate or payment receipt can be downloaded from that same request.\n"
                    . "- Citizens can message the office directly using the \"Chat\" button inside each request.\n"
                    . "- The language can be switched between English and Arabic using the button at the top of the page.\n\n";

            $representativeIds = Service::where('is_active', true)
                ->selectRaw('MIN(id) as id')
                ->groupBy(DB::raw("COALESCE(group_uuid, CONCAT('solo-', id))"))
                ->pluck('id');

            $services = Service::with('requiredDocuments')
                ->whereIn('id', $representativeIds)
                ->orderBy('name')
                ->get();

            $list = $isAr ? "الخدمات المتوفرة حاليًا:\n" : "Currently available services:\n";

            foreach ($services as $service) {
                $name = ($isAr && $service->name_ar) ? $service->name_ar : $service->name;
                $price = $service->price > 0
                    ? ($service->currency . ' ' . number_format($service->price, 2))
                    : ($isAr ? 'مجانية' : 'Free');
                $days = $service->processing_days;

                $docs = $service->requiredDocuments->map(function ($doc) use ($isAr) {
                    $docName = ($isAr && $doc->name_ar) ? $doc->name_ar : $doc->name;
                    if (!$doc->is_mandatory) {
                        $docName .= $isAr ? ' (اختياري)' : ' (optional)';
                    }
                    return $docName;
                })->implode(', ');

                if (!$docs) {
                    $docs = $isAr ? 'لا توجد مستندات مطلوبة' : 'no documents required';
                }

                $list .= $isAr
                    ? "- {$name}: السعر {$price}، مدة المعالجة تقريبًا {$days} يوم/أيام، المستندات المطلوبة: {$docs}.\n"
                    : "- {$name}: price {$price}, processing time approximately {$days} day(s), required documents: {$docs}.\n";
            }

            return $overview . $list;
        });
    }
}
