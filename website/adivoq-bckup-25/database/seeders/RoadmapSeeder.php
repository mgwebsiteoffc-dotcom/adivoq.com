<?php
// database/seeders/RoadmapSeeder.php

namespace Database\Seeders;

use App\Models\RoadmapItem;
use Illuminate\Database\Seeder;

class RoadmapSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'title' => 'Recurring Invoices',
                'description' => 'Automatically generate and send invoices on a schedule for retainer clients.',
                'category' => 'feature',
                'status' => 'planned',
                'priority' => 'high',
                'target_quarter' => 'Q2 2025',
            ],
            [
                'title' => 'WhatsApp Invoice Sending',
                'description' => 'Send invoices directly via WhatsApp with payment links.',
                'category' => 'feature',
                'status' => 'in_progress',
                'priority' => 'high',
                'target_quarter' => 'Q1 2025',
            ],
            [
                'title' => 'AI-Powered Insights',
                'description' => 'Get AI-generated insights about your revenue trends and brand relationships.',
                'category' => 'feature',
                'status' => 'planned',
                'priority' => 'medium',
                'target_quarter' => 'Q3 2025',
            ],
            [
                'title' => 'Multi-Currency Support',
                'description' => 'Full support for USD, EUR, GBP with automatic exchange rates.',
                'category' => 'improvement',
                'status' => 'completed',
                'priority' => 'high',
                'target_quarter' => 'Q4 2024',
            ],
            [
                'title' => 'Zapier Integration',
                'description' => 'Connect AdivoQ with 5000+ apps via Zapier.',
                'category' => 'integration',
                'status' => 'planned',
                'priority' => 'medium',
                'target_quarter' => 'Q2 2025',
            ],
            [
                'title' => 'Mobile App (iOS & Android)',
                'description' => 'Native mobile apps for managing invoices on the go.',
                'category' => 'feature',
                'status' => 'planned',
                'priority' => 'high',
                'target_quarter' => 'Q4 2025',
            ],
        ];

        foreach ($items as $item) {
            RoadmapItem::create($item);
        }

        $this->command->info('Roadmap items created.');
    }
}