<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\PublicSeo;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $items = PublicSeo::sitemapItems();

        return response()
            ->view('public.seo.sitemap', compact('items'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(): Response
    {
        $content = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin/',
            'Disallow: /dashboard/',
            'Disallow: /pay/',
            'Disallow: /webhooks/',
            'Disallow: /api/',
            '',
            'User-agent: GPTBot',
            'Allow: /',
            '',
            'User-agent: ChatGPT-User',
            'Allow: /',
            '',
            'User-agent: Google-Extended',
            'Allow: /',
            '',
            'User-agent: PerplexityBot',
            'Allow: /',
            '',
            'User-agent: ClaudeBot',
            'Allow: /',
            '',
            'Sitemap: '.route('seo.sitemap'),
            'Host: '.rtrim(config('app.url', url('/')), '/'),
        ]);

        return response($content)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function llms(): Response
    {
        $content = implode("\n", [
            '# '.PublicSeo::siteName(),
            '',
            'Public website for invoicing, payments, creator finance tools, blog content, and guides.',
            '',
            '## Crawling Policy',
            '- Public content may be crawled, summarized, and cited.',
            '- Admin, dashboard, payment, webhook, and API routes are not public knowledge pages.',
            '',
            '## Priority URLs',
            '- '.route('home'),
            '- '.route('blog.index'),
            '- '.route('guides.index'),
            '- '.route('tools.invoice-generator'),
            '- '.route('tools.tax-calculator'),
            '- '.route('tools.templates'),
            '- '.route('roadmap'),
            '- '.route('contact'),
            '',
            '## Sitemap',
            route('seo.sitemap'),
        ]);

        return response($content)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
