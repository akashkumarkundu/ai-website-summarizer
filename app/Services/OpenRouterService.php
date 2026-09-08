<?php

namespace App\Services;

use App\Contracts\AiSummarizerInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenRouterService implements AiSummarizerInterface
{
    /**
     * The OpenRouter API key.
     */
    protected ?string $apiKey;

    /**
     * The AI model to use.
     */
    protected string $model;

    /**
     * The base URL for the OpenRouter API.
     */
    protected string $baseUrl;

    /**
     * Timeout for the API request in seconds.
     */
    protected const REQUEST_TIMEOUT = 45;

    public function __construct(
        ?string $apiKey = null,
        ?string $model = null,
        ?string $baseUrl = null,
    ) {
        $this->apiKey = $apiKey ?? (string) config('services.openrouter.key', '');
        $this->model = $model ?? (string) config('services.openrouter.model', 'google/gemini-2.5-flash');
        $this->baseUrl = rtrim($baseUrl ?? (string) config('services.openrouter.base_url', 'https://openrouter.ai/api/v1'), '/');
    }

    /**
     * Summarize website content using OpenRouter Chat Completions API.
     *
     * @param  string  $language  Target language code ('bn' for Bengali, 'en' for English)
     *
     * @throws RuntimeException
     */
    public function summarize(string $text, string $title = '', string $url = '', string $language = 'bn'): string
    {
        if ($this->apiKey === '' || $this->apiKey === null) {
            throw new RuntimeException('OpenRouter API key is not configured. Please set OPENROUTER_API_KEY in your .env file.');
        }

        if ($language === 'bn') {
            $systemPrompt = <<<'PROMPT'
You are an expert AI website content summarizer. Your task is to analyze the provided webpage content and produce an accurate, clear, and well-structured summary written entirely in standard, fluent Bengali (বাংলা).

CRITICAL LANGUAGE RULE:
Write the entire output in Bengali (বাংলা). Do not use English headings or sentences, except for unavoidable proper nouns, technical terms, or brand names.

Format your response in GitHub-flavored Markdown using this structure:
### 📌 সারসংক্ষেপ (Overview)
ওয়েবসাইট বা পেজটির মূল বিষয়বস্তু ও উদ্দেশ্য নিয়ে সহজ ও প্রাঞ্জল বাংলায় ২-৩ বাক্যের একটি স্পষ্ট বিবরণ।

### 🔑 প্রধান বিষয়সমূহ (Key Highlights)
- ৪ থেকে ৬টি পরিষ্কার ও তথ্যবহুল বুলেট পয়েন্টে মূল তথ্য, ঘোষণা বা শিক্ষণীয় বিষয়গুলো তুলে ধরুন।

### 🎯 উদ্দিষ্ট পাঠক ও মূল বার্তা (Target Audience & Takeaways)
এই তথ্যটি কাদের জন্য সবচেয়ে বেশি কার্যকর এবং এর প্রধান বার্তা কী।
PROMPT;
            $languageName = 'Bengali (বাংলা)';
        } else {
            $systemPrompt = <<<'PROMPT'
You are an expert content summarizer. Analyze the provided webpage content and produce a clean, structured, and easy-to-read summary in GitHub-flavored Markdown.

Use this format:
### Overview
A clear 2-3 sentence overview explaining what the website or article is about.

### Key Points
- 4 to 6 concise bullet points highlighting the most important facts, takeaways, or insights.

### Audience & Takeaway
A brief conclusion on who would benefit most from this content and the primary takeaway.

Maintain an objective and professional tone.
PROMPT;
            $languageName = 'English';
        }

        $userPrompt = "Target Output Language: {$languageName}\nWebsite Title: {$title}\nWebsite URL: {$url}\n\nContent:\n{$text}";

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'HTTP-Referer' => config('app.url', 'http://localhost:8000'),
                'X-Title' => config('app.name', 'AI Website Summarizer'),
                'Content-Type' => 'application/json',
            ])
                ->timeout(self::REQUEST_TIMEOUT)
                ->post("{$this->baseUrl}/chat/completions", [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt,
                        ],
                        [
                            'role' => 'user',
                            'content' => $userPrompt,
                        ],
                    ],
                    'temperature' => 0.4,
                    'max_tokens' => 1500,
                ]);
        } catch (ConnectionException $e) {
            throw new RuntimeException('The AI request timed out or could not connect to OpenRouter. Please try again.', 0, $e);
        } catch (\Throwable $e) {
            throw new RuntimeException('AI service request failed: '.$e->getMessage(), 0, $e);
        }

        if ($response->failed()) {
            $status = $response->status();
            $errorMessage = $response->json('error.message') ?? $response->body();

            if ($status === 401) {
                throw new RuntimeException('Invalid OpenRouter API key. Please check your OPENROUTER_API_KEY in .env.');
            }

            if ($status === 402) {
                throw new RuntimeException('Insufficient OpenRouter credits. Please verify your OpenRouter account balance.');
            }

            if ($status === 429) {
                throw new RuntimeException('OpenRouter rate limit reached. Please wait a moment and try again.');
            }

            throw new RuntimeException("OpenRouter API error (HTTP {$status}): ".($errorMessage ?: 'Unknown error.'));
        }

        $summary = $response->json('choices.0.message.content');

        if (! is_string($summary) || trim($summary) === '') {
            throw new RuntimeException('Received an empty summary response from OpenRouter.');
        }

        return trim($summary);
    }

    /**
     * Get the human-readable provider name.
     */
    public function getProviderName(): string
    {
        return 'OpenRouter (Multi-Model)';
    }
}
