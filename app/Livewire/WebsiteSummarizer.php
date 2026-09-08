<?php

namespace App\Livewire;

use App\Services\OpenRouterService;
use App\Services\WebsiteContentFetcher;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class WebsiteSummarizer extends Component
{
    /**
     * The input URL entered by the user.
     */
    #[Validate]
    public string $url = '';

    /**
     * Extracted website title.
     */
    public ?string $websiteTitle = null;

    /**
     * AI-generated summary in markdown.
     */
    public ?string $summary = null;

    /**
     * Character count of extracted text.
     */
    public ?int $charCount = null;

    /**
     * The last successfully summarized URL.
     */
    public ?string $summarizedUrl = null;

    /**
     * User-facing error message.
     */
    public ?string $errorMessage = null;

    /**
     * Output language: 'bn' (Bengali) or 'en' (English).
     */
    public string $language = 'bn';

    /**
     * Validation rules.
     *
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'url' => ['required', 'string', 'url:http,https', 'max:2048'],
            'language' => ['required', 'string', 'in:bn,en'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'url.required' => 'Please enter a website URL.',
            'url.url' => 'Please enter a valid website URL starting with https:// or http://.',
            'url.max' => 'The website URL is too long.',
        ];
    }

    /**
     * Process the URL and generate an AI summary.
     */
    public function summarize(WebsiteContentFetcher $fetcher, OpenRouterService $ai): void
    {
        $trimmed = trim($this->url);
        if ($trimmed !== '' && ! preg_match('#^https?://#i', $trimmed) && str_contains($trimmed, '.')) {
            $trimmed = 'https://'.$trimmed;
            $this->url = $trimmed;
        }

        $this->validate();

        $this->errorMessage = null;
        $this->summary = null;
        $this->websiteTitle = null;
        $this->charCount = null;
        $this->summarizedUrl = null;

        try {
            $extracted = $fetcher->fetch($this->url);

            $this->websiteTitle = $extracted['title'];
            $this->charCount = $extracted['char_count'];
            $this->summarizedUrl = $extracted['url'];

            $this->summary = $ai->summarize(
                text: $extracted['text'],
                title: $extracted['title'],
                url: $extracted['url'],
                language: $this->language,
            );
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    /**
     * Reset the form and summary.
     */
    public function resetSummarizer(): void
    {
        $this->reset(['url', 'websiteTitle', 'summary', 'charCount', 'summarizedUrl', 'errorMessage']);
        $this->resetValidation();
    }

    /**
     * Set a sample URL to quickly test the summarizer.
     */
    public function setSampleUrl(string $sampleUrl): void
    {
        $this->url = $sampleUrl;
        $this->errorMessage = null;
    }

    public function render(): View
    {
        return view('livewire.website-summarizer');
    }
}
