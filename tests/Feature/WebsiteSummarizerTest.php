<?php

use App\Livewire\WebsiteSummarizer;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

test('home page displays the website summarizer', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('AI Website Summarizer')
        ->assertSee('Enter a website URL and get an AI-powered summary.')
        ->assertSee('Summarize Website');
});

test('successfully summarizes a website using mocked OpenRouter response', function () {
    config()->set('services.openrouter.key', 'test-openrouter-key');

    $fakeHtml = <<<'HTML'
<!DOCTYPE html>
<html>
<head><title>Laravel Framework Guide</title></head>
<body>
    <header><nav>Navigation Link</nav></header>
    <main>
        <h1>Laravel Overview</h1>
        <p>Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and truly fulfilling experience to be meaningful.</p>
        <p>Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects.</p>
    </main>
    <footer>Copyright 2026</footer>
</body>
</html>
HTML;

    $mockSummary = <<<'MARKDOWN'
### Overview
Laravel is a modern PHP web application framework known for its expressive and elegant syntax.

### Key Points
- Focuses on developer experience and productivity.
- Simplifies common web development tasks like routing, auth, and caching.
- Offers rich ecosystem support.

### Audience & Takeaway
Ideal for web developers looking to build scalable PHP applications efficiently.
MARKDOWN;

    Http::fake([
        'https://example.com*' => Http::response($fakeHtml, 200, ['Content-Type' => 'text/html']),
        'https://openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => $mockSummary,
                    ],
                ],
            ],
        ], 200),
    ]);

    Livewire::test(WebsiteSummarizer::class)
        ->set('url', 'https://example.com/guide')
        ->set('language', 'en')
        ->call('summarize')
        ->assertSet('websiteTitle', 'Laravel Framework Guide')
        ->assertSet('summary', $mockSummary)
        ->assertSee('Laravel is a modern PHP web application framework')
        ->assertSee('Summary Ready')
        ->assertSee('Copy Summary')
        ->assertHasNoErrors();
});

test('successfully summarizes a website in Bengali by default', function () {
    config()->set('services.openrouter.key', 'test-openrouter-key');

    $fakeHtml = '<html><head><title>এইচএসটিইউ ভর্তি</title></head><body><p>হাজী মোহাম্মদ দানেশ বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় স্নাতক ভর্তি সংক্রান্ত সকল তথ্য এখানে পাওয়া যাবে।</p></body></html>';
    $mockSummaryBn = "### 📌 সারসংক্ষেপ\nএটি একটি বিশ্ববিদ্যালয় ভর্তি নির্দেশিকা।";

    Http::fake([
        'https://example.com*' => Http::response($fakeHtml, 200, ['Content-Type' => 'text/html']),
        'https://openrouter.ai/api/v1/chat/completions' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => $mockSummaryBn,
                    ],
                ],
            ],
        ], 200),
    ]);

    Livewire::test(WebsiteSummarizer::class)
        ->set('url', 'https://example.com')
        ->assertSet('language', 'bn')
        ->call('summarize')
        ->assertSet('summary', $mockSummaryBn)
        ->assertSee('বাংলা সারসংক্ষেপ প্রস্তুত')
        ->assertSee('সারসংক্ষেপ কপি করুন')
        ->assertHasNoErrors();
});

test('validates that url is required and correctly formatted', function () {
    Livewire::test(WebsiteSummarizer::class)
        ->set('url', '')
        ->call('summarize')
        ->assertHasErrors(['url' => 'required']);

    Livewire::test(WebsiteSummarizer::class)
        ->set('url', 'not-a-valid-domain')
        ->call('summarize')
        ->assertHasErrors(['url' => 'url']);
});

test('blocks SSRF attempts to localhost and private IPs', function () {
    Livewire::test(WebsiteSummarizer::class)
        ->set('url', 'http://localhost:8000/secret')
        ->call('summarize')
        ->assertSet('summary', null)
        ->assertSee('Requests to local or internal network hostnames are not allowed');

    Livewire::test(WebsiteSummarizer::class)
        ->set('url', 'http://127.0.0.1:8000')
        ->call('summarize')
        ->assertSet('summary', null)
        ->assertSee('Requests to private, loopback, or reserved IP addresses are blocked');

    Livewire::test(WebsiteSummarizer::class)
        ->set('url', 'http://192.168.1.1/admin')
        ->call('summarize')
        ->assertSet('summary', null)
        ->assertSee('Requests to private, loopback, or reserved IP addresses are blocked');
});

test('handles unreachable or error status from target website gracefully', function () {
    Http::fake([
        'https://offline-domain.com*' => Http::response('Not Found', 404),
    ]);

    Livewire::test(WebsiteSummarizer::class)
        ->set('url', 'https://offline-domain.com')
        ->call('summarize')
        ->assertSet('summary', null)
        ->assertSee('The website returned an error status code (404)');
});

test('handles missing OpenRouter API key gracefully', function () {
    config()->set('services.openrouter.key', '');

    Http::fake([
        'https://example.com*' => Http::response('<html><head><title>Test</title></head><body><p>This is plenty of text to ensure it passes the minimum readable text extraction requirement.</p></body></html>', 200, ['Content-Type' => 'text/html']),
    ]);

    Livewire::test(WebsiteSummarizer::class)
        ->set('url', 'https://example.com')
        ->call('summarize')
        ->assertSet('summary', null)
        ->assertSee('OpenRouter API key is not configured');
});

test('handles OpenRouter API rate limit error gracefully', function () {
    config()->set('services.openrouter.key', 'valid-key');

    Http::fake([
        'https://example.com*' => Http::response('<html><head><title>Test</title></head><body><p>This is plenty of text to ensure it passes the minimum readable text extraction requirement.</p></body></html>', 200, ['Content-Type' => 'text/html']),
        'https://openrouter.ai/api/v1/chat/completions' => Http::response(['error' => ['message' => 'Rate limit exceeded']], 429),
    ]);

    Livewire::test(WebsiteSummarizer::class)
        ->set('url', 'https://example.com')
        ->call('summarize')
        ->assertSet('summary', null)
        ->assertSee('OpenRouter rate limit reached');
});
