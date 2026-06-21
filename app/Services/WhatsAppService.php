<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends WhatsApp Business notifications via Twilio.
 *
 * Two modes, both using the same .env-driven config (see
 * config/services.php):
 *
 *   - Sandbox / free-text mode: if TWILIO_CONTENT_SID is empty, sends a
 *     plain Body message. Only works within 24h of the recipient
 *     messaging first, or for numbers that joined the Sandbox — fine
 *     for early testing, not for proactive "your service is ready"
 *     notifications.
 *   - Production template mode: once TWILIO_CONTENT_SID is set (an
 *     approved WhatsApp Content Template, e.g. "E-Services Platform\n\n
 *     {{1}}"), every send() call goes out as a business-initiated
 *     template message with the composed text dropped into {{1}}. This
 *     is what makes automatic notifications (approved, payment
 *     confirmed, appointment scheduled, etc.) actually deliverable.
 *
 * If credentials are missing, send() simply logs and returns false
 * instead of throwing — a missing/misconfigured WhatsApp integration
 * should never break the underlying request/appointment/payment flow.
 *
 * Setup:
 *   1. Sandbox testing (free): https://www.twilio.com/docs/whatsapp/sandbox
 *   2. Production: register a WhatsApp Sender (Messaging > Senders >
 *      WhatsApp senders), then create + get approval for a Content
 *      Template (Messaging > Content Template Builder). Put its SID in
 *      TWILIO_CONTENT_SID and the sender's number in TWILIO_WHATSAPP_FROM.
 */
class WhatsAppService
{
    public static function send(string $toPhone, string $message): bool
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.auth_token');
        $from = config('services.twilio.whatsapp_from');
        $contentSid = config('services.twilio.content_sid');

        if (empty($sid) || empty($token) || empty($from)) {
            Log::info('WhatsApp notification skipped — TWILIO_SID / TWILIO_AUTH_TOKEN / TWILIO_WHATSAPP_FROM not configured.', [
                'to' => $toPhone,
            ]);
            return false;
        }

        $normalized = self::toE164($toPhone);
        if (!$normalized) {
            Log::warning('WhatsApp notification skipped — could not normalize phone number.', ['to' => $toPhone]);
            return false;
        }

        $payload = [
            'From' => 'whatsapp:' . $from,
            'To'   => 'whatsapp:+' . $normalized,
        ];

        if (!empty($contentSid)) {
            // Production template mode — required for business-initiated
            // messages (i.e. us messaging the citizen first).
            $payload['ContentSid'] = $contentSid;
            $payload['ContentVariables'] = json_encode(['1' => $message]);
        } else {
            // Sandbox / session-reply mode.
            $payload['Body'] = $message;
        }

        try {
            $response = Http::asForm()
                ->withBasicAuth($sid, $token)
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", $payload);

            if ($response->failed()) {
                Log::warning('WhatsApp send failed', ['to' => $normalized, 'response' => $response->body()]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('WhatsApp send exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Normalize a Lebanese phone number (any common format accepted by
     * App\Rules\LebanesePhoneNumber) into E.164 without the leading "+".
     */
    public static function toE164(string $phone): ?string
    {
        $normalized = preg_replace('/[\s\-\(\)]+/', '', $phone);

        if (preg_match('/^(?:\+?961|00961)?0?(3\d{6}|7[01689]\d{6}|81\d{6}|[1456789]\d{6,7})$/', $normalized, $m)) {
            return '961' . $m[1];
        }

        return null;
    }
}
