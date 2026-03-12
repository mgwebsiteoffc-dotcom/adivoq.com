<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\Guide;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PublicSeo
{
    public static function siteName(): string
    {
        return config('app.name', 'AdivoQ');
    }

    public static function siteUrl(): string
    {
        return rtrim(config('app.url', url('/')), '/');
    }

    public static function organizationSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            '@id' => self::siteUrl().'#organization',
            'name' => self::siteName(),
            'url' => self::siteUrl(),
            'logo' => asset('favicon.ico'),
        ];
    }

    public static function websiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => self::siteUrl().'#website',
            'url' => self::siteUrl(),
            'name' => self::siteName(),
            'publisher' => [
                '@id' => self::siteUrl().'#organization',
            ],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => route('blog.index').'?search={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    public static function webPageSchema(array $data = []): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => $data['type'] ?? 'WebPage',
            '@id' => ($data['url'] ?? url()->current()).'#webpage',
            'url' => $data['url'] ?? url()->current(),
            'name' => $data['name'] ?? null,
            'description' => $data['description'] ?? null,
            'isPartOf' => [
                '@id' => self::siteUrl().'#website',
            ],
            'about' => $data['about'] ?? null,
            'primaryImageOfPage' => !empty($data['image']) ? [
                '@type' => 'ImageObject',
                'url' => $data['image'],
            ] : null,
        ]);
    }

    public static function breadcrumbSchema(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(function ($item, $index) {
                return [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item['name'],
                    'item' => $item['url'],
                ];
            })->all(),
        ];
    }

    public static function collectionPageSchema(string $name, string $description, string $url): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            '@id' => $url.'#collection',
            'url' => $url,
            'name' => $name,
            'description' => $description,
            'isPartOf' => [
                '@id' => self::siteUrl().'#website',
            ],
        ];
    }

    public static function blogPostingSchema(BlogPost $post): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'mainEntityOfPage' => url()->current(),
            'headline' => $post->meta_title ?: $post->title,
            'description' => $post->meta_description ?: $post->excerpt,
            'datePublished' => optional($post->published_at)?->toAtomString(),
            'dateModified' => optional($post->updated_at)?->toAtomString(),
            'image' => $post->cover_image ? [asset('storage/'.$post->cover_image)] : [asset('favicon.ico')],
            'author' => [
                '@type' => 'Person',
                'name' => $post->author->name ?? self::siteName().' Team',
            ],
            'publisher' => [
                '@id' => self::siteUrl().'#organization',
            ],
            'articleSection' => $post->category->name ?? null,
            'wordCount' => str_word_count(strip_tags($post->content)),
        ];
    }

    public static function howToSchema(Guide $guide): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => $guide->title,
            'description' => $guide->description,
            'image' => $guide->cover_image ? [asset('storage/'.$guide->cover_image)] : [asset('favicon.ico')],
            'step' => $guide->steps->values()->map(function ($step, $index) {
                return [
                    '@type' => 'HowToStep',
                    'position' => $index + 1,
                    'name' => $step->title,
                    'text' => trim(strip_tags($step->content)),
                ];
            })->all(),
            'publisher' => [
                '@id' => self::siteUrl().'#organization',
            ],
        ];
    }

    public static function sitemapItems(): Collection
    {
        $staticPages = collect([
            ['loc' => route('home'), 'lastmod' => now(), 'changefreq' => 'weekly', 'priority' => '1.0'],
            ['loc' => route('blog.index'), 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => route('guides.index'), 'lastmod' => now(), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => route('roadmap'), 'lastmod' => now(), 'changefreq' => 'weekly', 'priority' => '0.7'],
            ['loc' => route('tools.tax-calculator'), 'lastmod' => now(), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('tools.invoice-generator'), 'lastmod' => now(), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => route('tools.templates'), 'lastmod' => now(), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => route('contact'), 'lastmod' => now(), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => route('privacy'), 'lastmod' => now(), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => route('terms'), 'lastmod' => now(), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => route('refund'), 'lastmod' => now(), 'changefreq' => 'yearly', 'priority' => '0.3'],
            ['loc' => route('hsn.search'), 'lastmod' => now(), 'changefreq' => 'weekly', 'priority' => '0.7'],
        ]);

        $blogPages = BlogPost::published()
            ->latest('updated_at')
            ->get()
            ->map(fn (BlogPost $post) => [
                'loc' => route('blog.show', $post->slug),
                'lastmod' => $post->updated_at ?? $post->published_at ?? now(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ]);

        $guidePages = Guide::published()
            ->latest('updated_at')
            ->get()
            ->map(fn (Guide $guide) => [
                'loc' => route('guides.show', $guide->slug),
                'lastmod' => $guide->updated_at ?? $guide->published_at ?? now(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ]);

        return $staticPages
            ->concat($blogPages)
            ->concat($guidePages)
            ->unique('loc')
            ->values()
            ->map(function (array $item) {
                $item['lastmod'] = $item['lastmod'] instanceof Carbon
                    ? $item['lastmod']->toAtomString()
                    : Carbon::parse($item['lastmod'])->toAtomString();

                return $item;
            });
    }
}
