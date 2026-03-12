<?php
// database/seeders/BlogSeeder.php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('is_system_admin', true)->first();

        $posts = [
            [
                'title' => 'GST for Content Creators: Complete Guide 2025',
                'excerpt' => 'Everything you need to know about GST registration, rates, and filing as a content creator in India.',
                'body' => '<p>As a content creator in India, understanding GST is crucial for your financial health...</p><h2>Do You Need GST Registration?</h2><p>If your annual turnover exceeds ₹20 lakhs (₹10 lakhs for special category states), GST registration is mandatory...</p>',
                'category' => 'tax-tips',
                'status' => 'published',
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'How to Price Your Brand Deals: A Creator\'s Guide',
                'excerpt' => 'Learn the art of pricing your sponsored content correctly based on engagement, reach, and deliverables.',
                'body' => '<p>Pricing your brand deals can be challenging, especially when you\'re starting out...</p><h2>Factors to Consider</h2><p>Your engagement rate, niche, and content type all play a role...</p>',
                'category' => 'guides',
                'status' => 'published',
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => 'TDS on Creator Income: What Brands Deduct and Why',
                'excerpt' => 'Understanding TDS deductions on your creator payments and how to claim them back.',
                'body' => '<p>When brands pay creators, they often deduct TDS (Tax Deducted at Source)...</p><h2>Common TDS Sections</h2><p>Section 194J applies to professional/technical services at 10%...</p>',
                'category' => 'tax-tips',
                'status' => 'published',
                'published_at' => now()->subDays(15),
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::create([
                'author_id' => $admin->id,
                'title' => $post['title'],
                'slug' => Str::slug($post['title']),
                'excerpt' => $post['excerpt'],
                'body' => $post['body'],
                'category' => $post['category'],
                'status' => $post['status'],
                'published_at' => $post['published_at'],
                'meta_title' => $post['title'] . ' | AdivoQ',
                'meta_description' => $post['excerpt'],
            ]);
        }

        $this->command->info('Blog posts created.');
    }
}