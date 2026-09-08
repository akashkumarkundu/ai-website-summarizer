<?php

namespace App\Contracts;

interface AiSummarizerInterface
{
    /**
     * Summarize extracted website content.
     *
     * @param  string  $text  Clean readable text from the webpage
     * @param  string  $title  Page title
     * @param  string  $url  Source website URL
     * @param  string  $language  Target language code ('bn' for Bengali, 'en' for English)
     * @return string Generated markdown summary
     */
    public function summarize(string $text, string $title = '', string $url = '', string $language = 'bn'): string;

    /**
     * Get the human-readable provider name.
     */
    public function getProviderName(): string;
}
