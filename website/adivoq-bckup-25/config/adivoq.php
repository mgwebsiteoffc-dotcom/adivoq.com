<?php
// config/adivoq.php

return [
    'name' => 'AdivoQ',
    'tagline' => 'Financial OS for Creators',
    'version' => '1.0.0',

    'currency' => [
        'default' => 'INR',
        'supported' => ['INR', 'USD', 'EUR', 'GBP', 'AED'],
        'symbols' => [
            'INR' => '₹',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'AED' => 'د.إ',
        ],
    ],

    'invoice' => [
        'default_prefix' => 'INV',
        'number_padding' => 4,
        'default_due_days' => 30,
        'payment_terms' => [
            'immediate' => 'Due on Receipt',
            'net_7' => 'Net 7 Days',
            'net_15' => 'Net 15 Days',
            'net_30' => 'Net 30 Days',
            'net_45' => 'Net 45 Days',
            'net_60' => 'Net 60 Days',
        ],
    ],

    'tax' => [
        'gst_rates' => [0, 5, 12, 18, 28],
        'default_gst_rate' => 18,
        'default_tds_rate' => 10,
        'tds_rates' => [
            '194C' => 2,
            '194J' => 10,
            '194H' => 5,
            '194O' => 1,
        ],
        'hsn_sac_default' => '998361',
    ],

    'campaign_types' => [
        'sponsored_post' => 'Sponsored Post',
        'brand_ambassador' => 'Brand Ambassador',
        'affiliate' => 'Affiliate Marketing',
        'product_review' => 'Product Review',
        'event_appearance' => 'Event Appearance',
        'content_licensing' => 'Content Licensing',
        'consulting' => 'Consulting',
        'other' => 'Other',
    ],

    'platforms' => [
        'instagram' => 'Instagram',
        'youtube' => 'YouTube',
        'twitter' => 'Twitter/X',
        'linkedin' => 'LinkedIn',
        'podcast' => 'Podcast',
        'blog' => 'Blog',
        'tiktok' => 'TikTok',
        'multi' => 'Multi-Platform',
        'other' => 'Other',
    ],

    'expense_categories' => [
        'equipment' => 'Equipment & Gear',
        'software' => 'Software & Tools',
        'travel' => 'Travel & Transport',
        'outsourcing' => 'Outsourcing / Freelancers',
        'ads' => 'Advertising & Promotion',
        'internet' => 'Internet & Phone',
        'office' => 'Office & Workspace',
        'education' => 'Courses & Learning',
        'other' => 'Other',
    ],
];