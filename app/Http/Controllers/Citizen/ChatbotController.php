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

        // Updated to use the 2.5 