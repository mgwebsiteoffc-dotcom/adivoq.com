<?php
// database/seeders/DemoTenantSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Milestone;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\TaxSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoTenantSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo user
        $user = User::create([
            'name' => 'Demo Creator',
            'email' => 'demo@adivoq.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'is_system_admin' => false,
            'email_verified_at' => now(),
        ]);

        // Create tenant
        $tenant = Tenant::create([
            'name' => 'Demo Creator Studio',
            'slug' => 'demo-creator',
            'owner_id' => $user->id,
            'plan' => 'pro',
            'status' => 'active',
            'settings' => [
                'currency' => 'INR',
                'timezone' => 'Asia/Kolkata',
            ],
        ]);

        // Update user with tenant_id
        $user->update(['tenant_id' => $tenant->id]);

        // Create tax settings
        TaxSetting::create([
            'tenant_id' => $tenant->id,
            'financial_year' => '2024-25',
            'pan_number' => 'ABCDE1234F',
            'gstin' => '27ABCDE1234F1Z5',
            'gst_registered' => true,
            'gst_rate' => 18,
            'state_code' => '27',
            'state_name' => 'Maharashtra',
            'tds_default_rate' => 10,
            'business_type' => 'individual',
            'bank_name' => 'HDFC Bank',
            'bank_account_number' => '1234567890123',
            'bank_ifsc' => 'HDFC0001234',
            'bank_branch' => 'Mumbai Main',
            'upi_id' => 'democreator@upi',
        ]);

        // Create brands
        $brands = [
            [
                'name' => 'Nike India',
                'contact_person' => 'Rahul Sharma',
                'email' => 'rahul@nike.com',
                'phone' => '+91 9876543210',
                'gstin' => '27AAACN1234F1Z5',
                'address' => [
                    'line1' => '123 MG Road',
                    'city' => 'Mumbai',
                    'state' => 'Maharashtra',
                    'state_code' => '27',
                    'country' => 'India',
                    'zip' => '400001',
                ],
            ],
            [
                'name' => 'Amazon India',
                'contact_person' => 'Priya Patel',
                'email' => 'priya@amazon.in',
                'phone' => '+91 9876543211',
                'gstin' => '29AABCA1234F1Z5',
                'address' => [
                    'line1' => '456 Brigade Road',
                    'city' => 'Bangalore',
                    'state' => 'Karnataka',
                    'state_code' => '29',
                    'country' => 'India',
                    'zip' => '560001',
                ],
            ],
            [
                'name' => 'Boat Lifestyle',
                'contact_person' => 'Amit Verma',
                'email' => 'amit@boat.com',
                'phone' => '+91 9876543212',
                'gstin' => '27AABCB1234F1Z5',
                'address' => [
                    'line1' => '789 Linking Road',
                    'city' => 'Mumbai',
                    'state' => 'Maharashtra',
                    'state_code' => '27',
                    'country' => 'India',
                    'zip' => '400050',
                ],
            ],
        ];

        foreach ($brands as $brandData) {
            $brand = Brand::withoutGlobalScope('tenant')->create([
                'tenant_id' => $tenant->id,
                ...$brandData,
                'payment_terms' => 'net_30',
                'status' => 'active',
            ]);

            // Create campaign for each brand
            $campaign = Campaign::withoutGlobalScope('tenant')->create([
                'tenant_id' => $tenant->id,
                'brand_id' => $brand->id,
                'title' => $brand->name . ' - Summer Campaign 2024',
                'description' => 'Summer promotional campaign with Instagram content.',
                'campaign_type' => 'sponsored_post',
                'platform' => 'instagram',
                'total_value' => rand(50000, 200000),
                'currency' => 'INR',
                'status' => 'active',
                'start_date' => now()->subDays(rand(10, 30)),
                'end_date' => now()->addDays(rand(30, 60)),
            ]);

            // Create milestones
            $milestones = [
                ['title' => 'Instagram Reel 1', 'amount' => $campaign->total_value * 0.3, 'status' => 'completed'],
                ['title' => 'Instagram Reel 2', 'amount' => $campaign->total_value * 0.3, 'status' => 'completed'],
                ['title' => 'Story Posts (5)', 'amount' => $campaign->total_value * 0.2, 'status' => 'in_progress'],
                ['title' => 'Final Carousel Post', 'amount' => $campaign->total_value * 0.2, 'status' => 'pending'],
            ];

            foreach ($milestones as $index => $milestoneData) {
                Milestone::withoutGlobalScope('tenant')->create([
                    'tenant_id' => $tenant->id,
                    'campaign_id' => $campaign->id,
                    'title' => $milestoneData['title'],
                    'amount' => $milestoneData['amount'],
                    'status' => $milestoneData['status'],
                    'due_date' => now()->addDays(($index + 1) * 7),
                    'completed_at' => $milestoneData['status'] === 'completed' ? now()->subDays(rand(1, 10)) : null,
                    'sort_order' => $index,
                ]);
            }

            // Create invoice (only for first brand for simplicity)
            if ($brand->name === 'Nike India') {
                $invoice = Invoice::withoutGlobalScope('tenant')->create([
                    'tenant_id' => $tenant->id,
                    'brand_id' => $brand->id,
                    'campaign_id' => $campaign->id,
                    'invoice_number' => 'INV-2024-0001',
                    'public_token' => Str::random(32),
                    'issue_date' => now()->subDays(15),
                    'due_date' => now()->addDays(15),
                    'subtotal' => 100000,
                    'discount_type' => null,
                    'discount_value' => 0,
                    'discount_amount' => 0,
                    'taxable_amount' => 100000,
                    'cgst_rate' => 9,
                    'cgst_amount' => 9000,
                    'sgst_rate' => 9,
                    'sgst_amount' => 9000,
                    'igst_rate' => 0,
                    'igst_amount' => 0,
                    'tax_amount' => 18000,
                    'tds_rate' => 10,
                    'tds_amount' => 10000,
                    'total_amount' => 118000,
                    'amount_paid' => 60000,
                    'balance_due' => 48000, // 118000 - 60000 - 10000 TDS
                    'currency' => 'INR',
                    'status' => 'partially_paid',
                    'notes' => 'Payment for Instagram campaign - Phase 1',
                    'sent_at' => now()->subDays(14),
                ]);

                // Create invoice items
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => 'Instagram Reel - 60 seconds (2 Reels)',
                    'hsn_sac_code' => '998361',
                    'quantity' => 2,
                    'unit_price' => 30000,
                    'amount' => 60000,
                ]);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => 'Instagram Story Posts (5 Stories)',
                    'hsn_sac_code' => '998361',
                    'quantity' => 5,
                    'unit_price' => 8000,
                    'amount' => 40000,
                ]);

                // Create payment
                Payment::withoutGlobalScope('tenant')->create([
                    'tenant_id' => $tenant->id,
                    'invoice_id' => $invoice->id,
                    'brand_id' => $brand->id,
                    'campaign_id' => $campaign->id,
                    'amount' => 60000,
                    'currency' => 'INR',
                    'payment_method' => 'bank_transfer',
                    'payment_date' => now()->subDays(5),
                    'transaction_id' => 'NEFT' . rand(100000000, 999999999),
                    'tds_deducted' => 10000,
                    'status' => 'confirmed',
                ]);
            }
        }

        $this->command->info('Demo tenant created: demo@adivoq.com / password');
    }
}