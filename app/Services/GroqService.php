<?php

namespace App\Services;

use App\Contracts\AiSummarizerInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GroqService implements AiSummarizerInterface
{
    /**
     * The Groq API key.
     */
    protected ?string $apiKey;

    /**
     * The Groq model to use.
     */
    protected string $model;

    /**
     * The base URL for the Groq API.
     */
    protected string $baseUrl;

    /**
     * Timeout for the API request in seconds.
     */
    protected const REQUEST_TIMEOUT = 30;

    public function __construct(
        ?string $apiKey = null,
        ?string $model = null,
        ?string $baseUrl = null,
    ) {
        $this->apiKey = $apiKey ?? (string) config('services.groq.key', '');
        $this->model = $model ?? (string) config('services.groq.model', 'qwen/qwen3.8-27b');
        $this->baseUrl = rtrim($baseUrl ?? (string) config('services.groq.base_url', 'https://api.groq.com/openai/v1'), '/');
    }

    /**
     * Summarize website content using Groq's high-speed inference API.
     *
     * @param  string  $text  Clean readable text from the webpage
     * @param  string  $title  Page title
     * @param  string  $url  Source website URL
     * @param  string  $language  Target language code ('bn' for Bengali, 'en' for English)
     *
     * @throws RuntimeException
     */
    public function summarize(string $text, string $title = '', string $url = '', string $language = 'bn'): string
    {
        if ($this->apiKey === '' || $this->apiKey === null) {
            throw new RuntimeException('Groq API key is not configured. Please add GROQ_API_KEY in your .env file or switch to OpenRouter.');
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
### 📌 Overview
A clear 2-3 sentence overview explaining what the website or article is about.

### 🔑 Key Highlights
- 4 to 6 concise bullet points highlighting the most important facts, takeaways, or insights.

### 🎯 Audience & Takeaways
A brief conclusion on who would benefit most from this content and the primary takeaway.

Maintain an objective and professional tone.
PROMPT;
            $languageName = 'English';
        }

        $userPrompt = "Target Output Language: {$languageName}\nWebsite Title: {$title}\nWebsite URL: {$url}\n\nContent:\n{$text}";

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
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
                    'temperature' => 0.3,
                    'max_tokens' => 1500,
                ]);
        } catch (ConnectionException $e) {
            throw new RuntimeException('The request timed out or could not connect to Groq. Please check your network and try again.', 0, $e);
        } catch (\Throwable $e) {
            throw new RuntimeException('Groq AI service request failed: '.$e->getMessage(), 0, $e);
        }

        if ($response->failed()) {
            $status = $response->status();
            $errorMessage = $response->json('error.message') ?? $response->body();

            if ($status === 401) {
                throw new RuntimeException('Invalid Groq API key. Please check your GROQ_API_KEY in .env.');
            }

            if ($status === 429) {
                throw new RuntimeException('Groq rate limit reached. Please wait a few seconds and try again.');
            }

            throw new RuntimeException("Groq API error (HTTP {$status}): ".($errorMessage ?: 'Unknown error.'));
        }

        $summary = $response->json('choices.0.message.content');

        if (! is_string($summary) || trim($summary) === '') {
            throw new RuntimeException('Received an empty summary response from Groq.');
        }

        return trim($summary);
    }

    /**
     * Get the human-readable provider name.
     */
    public function getProviderName(): string
    {
        return 'Groq (LPU™ Fast)';
    }
}
