<?php

return [
    'name' => env('APP_NAME', 'InvoiceHero'),

    'plans' => [
        'free' => [
            'name' => 'Free',
            'price' => 0,
            'invoices_per_month' => 5,
            'brands' => 3,
            'team_members' => 0,
            'features' => ['basic_invoicing', 'pdf_download'],
        ],
        'starter' => [
            'name' => 'Starter',
            'price' => 499,
            'invoices_per_month' => 50,
            'brands' => 10,
            'team_members' => 2,
            'features' => ['basic_invoicing', 'pdf_download', 'email_sending', 'payment_links', 'reports'],
        ],
        'professional' => [
            'name' => 'Professional',
            'price' => 999,
            'invoices_per_month' => -1, // unlimited
            'brands' => -1,
            'team_members' => 5,
            'features' => ['basic_invoicing', 'pdf_download', 'email_sending', 'whatsapp_sending', 'payment_links', 'payment_gateway', 'reports', 'expense_tracking', 'recurring_invoices'],
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'price' => 2499,
            'invoices_per_month' => -1,
            'brands' => -1,
            'team_members' => -1,
            'features' => ['all'],
        ],
    ],

    'currencies' => [
        'INR' => ['symbol' => '₹', 'name' => 'Indian Rupee'],
        'USD' => ['symbol' => '$', 'name' => 'US Dollar'],
        'EUR' => ['symbol' => '€', 'name' => 'Euro'],
        'GBP' => ['symbol' => '£', 'name' => 'British Pound'],
    ],

    'default_currency' => 'INR',

    'gst_rates' => [0, 5, 12, 18, 28],
    'default_gst_rate' => 18,
    'default_tds_rate' => 10,

    'indian_states' => [
        '01' => 'Jammu & Kashmir', '02' => 'Himachal Pradesh', '03' => 'Punjab',
        '04' => 'Chandigarh', '05' => 'Uttarakhand', '06' => 'Haryana',
        '07' => 'Delhi', '08' => 'Rajasthan', '09' => 'Uttar Pradesh',
        '10' => 'Bihar', '11' => 'Sikkim', '12' => 'Arunachal Pradesh',
        '13' => 'Nagaland', '14' => 'Manipur', '15' => 'Mizoram',
        '16' => 'Tripura', '17' => 'Meghalaya', '18' => 'Assam',
        '19' => 'West Bengal', '20' => 'Jharkhand', '21' => 'Odisha',
        '22' => 'Chhattisgarh', '23' => 'Madhya Pradesh', '24' => 'Gujarat',
        '26' => 'Dadra & Nagar Haveli and Daman & Diu', '27' => 'Maharashtra',
        '28' => 'Andhra Pradesh (Old)', '29' => 'Karnataka', '30' => 'Goa',
        '31' => 'Lakshadweep', '32' => 'Kerala', '33' => 'Tamil Nadu',
        '34' => 'Puducherry', '35' => 'Andaman & Nicobar',
        '36' => 'Telangana', '37' => 'Andhra Pradesh',
    ],

    'payment_terms' => [
        'due_on_receipt' => 'Due on Receipt',
        'net_7' => 'Net 7',
        'net_15' => 'Net 15',
        'net_30' => 'Net 30',
        'net_45' => 'Net 45',
        'net_60' => 'Net 60',
        'custom' => 'Custom',
    ],

    'platforms' => [
        'youtube' => 'YouTube',
        'instagram' => 'Instagram',
        'twitter' => 'Twitter/X',
        'linkedin' => 'LinkedIn',
        'tiktok' => 'TikTok',
        'facebook' => 'Facebook',
        'podcast' => 'Podcast',
        'blog' => 'Blog',
        'other' => 'Other',
    ],
];