<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use RuntimeException;

class WebsiteContentFetcher
{
    /**
     * Maximum number of characters to extract and send to the AI.
     */
    protected const MAX_TEXT_LENGTH = 12000;

    /**
     * Timeout for the HTTP request in seconds.
     */
    protected const REQUEST_TIMEOUT = 15;

    /**
     * HTML elements to remove before extracting readable text.
     */
    protected const UNWANTED_TAGS = [
        'script',
        'style',
        'svg',
        'noscript',
        'iframe',
        'nav',
        'footer',
        'header',
        'aside',
        'form',
        'button',
        'template',
        'canvas',
    ];

    /**
     * Fetch a public website and extract readable text.
     *
     * @return array{url: string, title: string, text: string, char_count: int}
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function fetch(string $url): array
    {
        $normalizedUrl = $this->normalizeAndValidateUrl($url);

        $this->guardAgainstSsrf($normalizedUrl);

        try {
            $response = Http::timeout(self::REQUEST_TIMEOUT)
                ->withUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36')
                ->withHeaders([
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.9',
                ])
                ->get($normalizedUrl);
        } catch (ConnectionException $e) {
            throw new RuntimeException('Unable to reach the website. It might be offline, blocking automated requests, or timing out.', 0, $e);
        } catch (\Throwable $e) {
            throw new RuntimeException('Failed to fetch the website: '.$e->getMessage(), 0, $e);
        }

        if ($response->failed()) {
            throw new RuntimeException("The website returned an error status code ({$response->status()}). Please check the URL and try again.");
        }

        $contentType = (string) $response->header('Content-Type');
        if ($contentType !== '' && ! str_contains(strtolower($contentType), 'text/html') && ! str_contains(strtolower($contentType), 'text/plain')) {
            throw new RuntimeException('The provided URL does not return HTML or readable text content.');
        }

        $html = (string) $response->body();
        if (trim($html) === '') {
            throw new RuntimeException('The target website returned an empty response.');
        }

        $extracted = $this->extractReadableContent($html);

        if (mb_strlen($extracted['text']) < 50) {
            throw new RuntimeException('Could not extract sufficient readable text from this webpage. It may rely entirely on client-side JavaScript (SPA) or require a login.');
        }

        return [
            'url' => $normalizedUrl,
            'title' => $extracted['title'],
            'text' => $extracted['text'],
            'char_count' => mb_strlen($extracted['text']),
        ];
    }

    /**
     * Normalize and validate URL structure.
     */
    public function normalizeAndValidateUrl(string $url): string
    {
        $trimmed = trim($url);

        if ($trimmed === '') {
            throw new InvalidArgumentException('Please provide a website URL.');
        }

        if (! preg_match('#^https?://#i', $trimmed)) {
            $trimmed = 'https://'.$trimmed;
        }

        if (! filter_var($trimmed, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('The provided URL is not valid. Please include a valid domain (e.g. https://example.com).');
        }

        $parsed = parse_url($trimmed);
        $scheme = strtolower($parsed['scheme'] ?? '');
        $host = $parsed['host'] ?? '';

        if (! in_array($scheme, ['http', 'https'], true)) {
            throw new InvalidArgumentException('Only HTTP and HTTPS URLs are supported.');
        }

        if ($host === '') {
            throw new InvalidArgumentException('The URL must include a valid hostname.');
        }

        return $trimmed;
    }

    /**
     * Protect against SSRF and private/local IP address access.
     */
    protected function guardAgainstSsrf(string $url): void
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! $host) {
            throw new InvalidArgumentException('Invalid host in URL.');
        }

        $hostLower = strtolower($host);

        // Check reserved/local hostnames
        if (
            $hostLower === 'localhost' ||
            str_ends_with($hostLower, '.localhost') ||
            str_ends_with($hostLower, '.local') ||
            str_ends_with($hostLower, '.internal') ||
            str_ends_with($hostLower, '.test')
        ) {
            throw new InvalidArgumentException('Requests to local or internal network hostnames are not allowed.');
        }

        // Direct IP check
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $this->validateIpAddress($host);

            return;
        }

        // DNS resolution check
        $ips = @gethostbynamel($host);

        if ($ips === false || empty($ips)) {
            // Allow tests using mock domains to proceed to Http::fake
            if (app()->runningUnitTests()) {
                return;
            }

            throw new RuntimeException("Could not resolve domain name: {$host}. Please make sure the domain exists.");
        }

        foreach ($ips as $ip) {
            $this->validateIpAddress($ip);
        }
    }

    /**
     * Validate an IP address is public and not reserved or private.
     */
    protected function validateIpAddress(string $ip): void
    {
        $isSafePublicIp = filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );

        if ($isSafePublicIp === false || str_starts_with($ip, '127.') || str_starts_with($ip, '169.254.') || $ip === '0.0.0.0') {
            throw new InvalidArgumentException('Requests to private, loopback, or reserved IP addresses are blocked for security.');
        }
    }

    /**
     * Extract page title and readable content from raw HTML.
     *
     * @return array{title: string, text: string}
     */
    protected function extractReadableContent(string $html): array
    {
        // Extract title
        $title = '';
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
            $title = trim(html_entity_decode(strip_tags($matches[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        }

        // Parse HTML using DOMDocument
        $dom = new DOMDocument;
        $previousLibXmlErrors = libxml_use_internal_errors(true);

        // Convert encoding to utf-8 safely
        $encodedHtml = mb_encode_numericentity($html, [0x80, 0x10FFFF, 0, 0x1FFFFF], 'UTF-8');
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$encodedHtml, LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NONET);

        libxml_clear_errors();
        libxml_use_internal_errors($previousLibXmlErrors);

        // Remove unwanted elements
        $xpath = new DOMXPath($dom);
        foreach (self::UNWANTED_TAGS as $tag) {
            $elements = $xpath->query('//'.$tag);
            if ($elements) {
                for ($i = $elements->length - 1; $i >= 0; $i--) {
                    $node = $elements->item($i);
                    if ($node instanceof DOMElement && $node->parentNode) {
                        $node->parentNode->removeChild($node);
                    }
                }
            }
        }

        // Look for main semantic content first
        $contentNodes = $xpath->query('//main | //article | //*[@role="main"]');
        $rawText = '';

        if ($contentNodes && $contentNodes->length > 0) {
            foreach ($contentNodes as $contentNode) {
                $rawText .= ' '.$contentNode->textContent;
            }
        }

        // Fallback to body content if semantic main/article wasn't found or was sparse
        if (mb_strlen(trim($rawText)) < 150) {
            $bodyNodes = $xpath->query('//body');
            if ($bodyNodes && $bodyNodes->length > 0) {
                $rawText = $bodyNodes->item(0)->textContent ?? '';
            } else {
                $rawText = $dom->textContent ?? '';
            }
        }

        // Clean up whitespace and excessive line breaks
        $cleanText = preg_replace('/[ \t]+/', ' ', $rawText);
        $cleanText = preg_replace('/\n\s*\n/', "\n\n", (string) $cleanText);
        $cleanText = trim((string) $cleanText);

        // Limit character length to prevent token overflow
        if (mb_strlen($cleanText) > self::MAX_TEXT_LENGTH) {
            $cleanText = mb_substr($cleanText, 0, self::MAX_TEXT_LENGTH);
            // Cut cleanly at last whitespace
            $lastSpace = mb_strrpos($cleanText, ' ');
            if ($lastSpace !== false) {
                $cleanText = mb_substr($cleanText, 0, $lastSpace);
            }
            $cleanText .= "\n\n[Content truncated for length...]";
        }

        return [
            'title' => $title !== '' ? $title : 'Untitled Webpage',
            'text' => $cleanText,
        ];
    }
}
