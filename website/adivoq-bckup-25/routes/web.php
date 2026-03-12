<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Tenant\BrandController;
use App\Http\Controllers\Tenant\CampaignController;
use App\Http\Controllers\Tenant\MilestoneController;
use App\Http\Controllers\Tenant\InvoiceController;
use App\Http\Controllers\Tenant\PaymentController;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Tenant\RecurringInvoiceController;
use App\Http\Controllers\Tenant\ExpenseController;
use App\Http\Controllers\Tenant\ReportsController;
use App\Http\Controllers\Tenant\TeamController;




/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Landing Page
Route::get('/', function () {
    return view('public.landing');
})->name('home');

// Blog
Route::get('/blog', function () {
    $posts = \App\Models\BlogPost::where('status', 'published')
        ->orderBy('published_at', 'desc')
        ->paginate(9);
    return view('public.blog.index', compact('posts'));
})->name('blog');

Route::get('/blog/{slug}', function ($slug) {
    $post = \App\Models\BlogPost::where('slug', $slug)
        ->where('status', 'published')
        ->firstOrFail();
    $post->increment('views_count');
    return view('public.blog.show', compact('post'));
})->name('blog.show');

// Roadmap
Route::get('/roadmap', function () {
    $items = \App\Models\RoadmapItem::orderByRaw("FIELD(status, 'in_progress', 'planned', 'completed', 'cancelled')")
        ->orderByRaw("FIELD(priority, 'critical', 'high', 'medium', 'low')")
        ->get();
    return view('public.roadmap', compact('items'));
})->name('roadmap');

// Contact
Route::get('/contact', function () {
    return view('public.contact');
})->name('contact');

Route::post('/contact', function () {
    request()->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'subject' => 'nullable|string|max:255',
        'message' => 'required|string|max:2000',
    ]);

    \App\Models\ContactMessage::create([
        'name' => request('name'),
        'email' => request('email'),
        'subject' => request('subject'),
        'message' => request('message'),
    ]);

    return back()->with('success', 'Thank you! Your message has been sent. We\'ll get back to you soon.');
})->name('contact.submit');

// Waitlist
Route::post('/waitlist', function () {
    request()->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:waitlist,email',
        'creator_type' => 'nullable|string|max:100',
    ]);

    \App\Models\Waitlist::create([
        'name' => request('name'),
        'email' => request('email'),
        'creator_type' => request('creator_type'),
        'source' => 'landing',
    ]);

    return back()->with('success', 'You\'re on the list! We\'ll notify you when we launch new features.');
})->name('waitlist.submit');

// Tools
Route::get('/tools/tax-calculator', function () {
    return view('public.tools.tax-calculator');
})->name('tools.tax-calculator');

Route::get('/tools/invoice-generator', function () {
    return view('public.tools.invoice-generator');
})->name('tools.invoice-generator');

// Legal Pages
Route::get('/privacy-policy', function () {
    return view('public.privacy-policy');
})->name('privacy-policy');

Route::get('/terms-of-service', function () {
    return view('public.terms-of-service');
})->name('terms-of-service');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        $stats = [
            'tenants' => \App\Models\Tenant::count(),
            'active_tenants' => \App\Models\Tenant::where('status', 'active')->count(),
            'users' => \App\Models\User::count(),
            'waitlist' => \App\Models\Waitlist::count(),
            'messages' => \App\Models\ContactMessage::where('status', 'new')->count(),
            'blog_posts' => \App\Models\BlogPost::where('status', 'published')->count(),
        ];
        $recentTenants = \App\Models\Tenant::with('owner')->latest()->take(5)->get();
        $recentMessages = \App\Models\ContactMessage::latest()->take(5)->get();
        return view('admin.dashboard', compact('stats', 'recentTenants', 'recentMessages'));
    })->name('dashboard');

    // Tenants
    Route::resource('tenants', TenantController::class);
    Route::post('/tenants/{tenant}/suspend', [TenantController::class, 'suspend'])->name('tenants.suspend');
    Route::post('/tenants/{tenant}/activate', [TenantController::class, 'activate'])->name('tenants.activate');
    Route::post('/tenants/{tenant}/impersonate', [TenantController::class, 'impersonate'])->name('tenants.impersonate');

    // Blog
    Route::resource('blog', BlogController::class)->except(['show']);

    // Roadmap (Simple CRUD)
    Route::get('/roadmap', function () {
        $items = \App\Models\RoadmapItem::latest()->paginate(20);
        return view('admin.roadmap.index', compact('items'));
    })->name('roadmap.index');

    Route::get('/roadmap/create', function () {
        return view('admin.roadmap.create');
    })->name('roadmap.create');

    Route::post('/roadmap', function () {
        request()->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'status' => 'required|string',
            'priority' => 'required|string',
            'target_quarter' => 'nullable|string',
        ]);
        \App\Models\RoadmapItem::create(request()->all());
        return redirect()->route('admin.roadmap.index')->with('success', 'Roadmap item created.');
    })->name('roadmap.store');

    Route::get('/roadmap/{roadmapItem}/edit', function (\App\Models\RoadmapItem $roadmapItem) {
        return view('admin.roadmap.edit', ['item' => $roadmapItem]);
    })->name('roadmap.edit');

    Route::put('/roadmap/{roadmapItem}', function (\App\Models\RoadmapItem $roadmapItem) {
        request()->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'status' => 'required|string',
            'priority' => 'required|string',
            'target_quarter' => 'nullable|string',
        ]);
        $roadmapItem->update(request()->all());
        return redirect()->route('admin.roadmap.index')->with('success', 'Roadmap item updated.');
    })->name('roadmap.update');

    Route::delete('/roadmap/{roadmapItem}', function (\App\Models\RoadmapItem $roadmapItem) {
        $roadmapItem->delete();
        return redirect()->route('admin.roadmap.index')->with('success', 'Roadmap item deleted.');
    })->name('roadmap.destroy');

    // Waitlist
    Route::get('/waitlist', function () {
        $entries = \App\Models\Waitlist::latest()->paginate(30);
        return view('admin.waitlist.index', compact('entries'));
    })->name('waitlist.index');

    Route::delete('/waitlist/{waitlist}', function (\App\Models\Waitlist $waitlist) {
        $waitlist->delete();
        return back()->with('success', 'Entry removed.');
    })->name('waitlist.destroy');

    // Messages
    Route::get('/messages', function () {
        $messages = \App\Models\ContactMessage::latest()->paginate(20);
        return view('admin.messages.index', compact('messages'));
    })->name('messages.index');

    Route::get('/messages/{contactMessage}', function (\App\Models\ContactMessage $contactMessage) {
        $contactMessage->update(['status' => 'read']);
        return view('admin.messages.show', ['message' => $contactMessage]);
    })->name('messages.show');

    Route::delete('/messages/{contactMessage}', function (\App\Models\ContactMessage $contactMessage) {
        $contactMessage->delete();
        return redirect()->route('admin.messages.index')->with('success', 'Message deleted.');
    })->name('messages.destroy');

    // Admin Users
    Route::get('/users', function () {
        $users = \App\Models\User::where('is_system_admin', true)->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    })->name('users.index');

    // Settings
    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('settings');
    
    Route::get('admin/billing', function () {
    $tenants = \App\Models\Tenant::where('plan', 'pro')->get();
    return view('admin.billing', compact('tenants'));
})->name('billing');

// Activity Logs
    Route::get('/logs', function () {
        $logs = \App\Models\ActivityLog::with('user')->latest()->paginate(50);
        return view('admin.logs.index', compact('logs'));
    })->name('logs.index');
    
    Route::post('/settings/clear-cache', function () {
    \Artisan::call('config:clear');
    \Artisan::call('cache:clear');
    \Artisan::call('view:clear');
    \Artisan::call('route:clear');
    
    return back()->with('success', 'Cache cleared successfully.');
})->name('settings.clear-cache');



});

/*
|--------------------------------------------------------------------------
| Tenant (Creator Dashboard) Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'tenant'])->prefix('dashboard')->name('tenant.')->group(function () {
    
  Route::get('/', function () {

    $payments = \App\Models\Payment::all();
    $invoices = \App\Models\Invoice::with('brand')->get();
    $expenses = \App\Models\Expense::all();

    $stats = [
        'revenue'  => $payments->sum('amount'),
        'expenses' => $expenses->sum('amount'),
        'profit'   => $payments->sum('amount') - $expenses->sum('amount'),
        'invoices' => $invoices->count(),
        'brands'   => \App\Models\Brand::count(),
        'pending'  => $invoices->whereNotIn('status', ['paid', 'cancelled'])->sum('balance_due'),
    ];
    
 

    $overdueCount = $invoices->filter(function ($inv) {
        return $inv->status !== 'paid' && $inv->due_date && now()->greaterThan($inv->due_date);
    })->count();

    $recentInvoices = $invoices->sortByDesc('created_at')->take(4);

    $monthlyRevenue = $payments
        ->groupBy(function ($p) {
            return \Carbon\Carbon::parse($p->payment_date)->format('M');
        })
        ->map(function ($group) {
            return $group->sum('amount');
        });
        
          // ✅ Brand-wise revenue
    $brandRevenue = $payments
        ->groupBy(function ($p) {
            return $p->brand->name ?? 'Unknown';
        })
        ->map(function ($group) {
            return $group->sum('amount');
        });

    // ✅ Invoice aging
    $aging = [
        'current' => 0,
        '30' => 0,
        '60' => 0,
        '90' => 0,
    ];

    foreach ($invoices as $invoice) {
        if ($invoice->status == 'paid') continue;

        $days = now()->diffInDays($invoice->due_date, false);

        if ($days >= 0) {
            $aging['current'] += $invoice->balance_due;
        } elseif ($days >= -30) {
            $aging['30'] += $invoice->balance_due;
        } elseif ($days >= -60) {
            $aging['60'] += $invoice->balance_due;
        } else {
            $aging['90'] += $invoice->balance_due;
        }
    }


// Estimate tax inside dashboard
$totalRevenue = $stats['revenue'];
$totalExpenses = $stats['expenses'];
$netIncome = $totalRevenue - $totalExpenses;

// Estimate Income Tax (same logic as tax page)
$estimatedIncomeTax = 0;

if ($netIncome > 300000) {
    if ($netIncome <= 700000) {
        $estimatedIncomeTax = ($netIncome - 300000) * 0.05;
    } elseif ($netIncome <= 1000000) {
        $estimatedIncomeTax = 20000 + ($netIncome - 700000) * 0.10;
    } elseif ($netIncome <= 1200000) {
        $estimatedIncomeTax = 50000 + ($netIncome - 1000000) * 0.15;
    } elseif ($netIncome <= 1500000) {
        $estimatedIncomeTax = 80000 + ($netIncome - 1200000) * 0.20;
    } else {
        $estimatedIncomeTax = 140000 + ($netIncome - 1500000) * 0.30;
    }
}

// Calculate TDS
$tdsTotal = \App\Models\Invoice::sum('tds_amount');

$netTaxLiability = $estimatedIncomeTax - $tdsTotal;

$advanceTaxWarning = $netTaxLiability > 10000;

    return view('tenant.dashboard', compact(
        'stats',
        'overdueCount',
        'recentInvoices',
        'monthlyRevenue',
        'brandRevenue',
        'aging',
        'advanceTaxWarning'
    ));

})->name('tenant.dashboard');


    
        Route::resource('brands', BrandController::class);




Route::resource('campaigns', CampaignController::class);

Route::prefix('campaigns/{campaign}')
    ->name('campaigns.')
    ->group(function () {

    Route::post('/milestones', [MilestoneController::class, 'store'])
        ->name('milestones.store');

    Route::put('/milestones/{milestone}', [MilestoneController::class, 'update'])
        ->name('milestones.update');

    Route::delete('/milestones/{milestone}', [MilestoneController::class, 'destroy'])
        ->name('milestones.destroy');

    Route::post('/milestones/{milestone}/complete', [MilestoneController::class, 'complete'])
        ->name('milestones.complete');
});

    
    Route::resource('invoices', InvoiceController::class);


    Route::get('/invoices/create', function () {
        $brands = \App\Models\Brand::where('status', 'active')->orderBy('name')->get();
        $campaigns = \App\Models\Campaign::where('status', 'active')->orderBy('title')->get();
        return view('tenant.invoices.create', compact('brands', 'campaigns'));
    })->name('invoices.create');
    
            // ✅ ADD PDF ROUTE HERE
        Route::get('invoices/{invoice}/pdf', function (\App\Models\Invoice $invoice) {

            $invoice->load(['brand', 'items']);

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', compact('invoice'));

            return $pdf->download($invoice->invoice_number . '.pdf');

        })->name('invoices.pdf');
        
        Route::post('invoices/{invoice}/send', function (\App\Models\Invoice $invoice) {

    if ($invoice->status === 'draft') {
        $invoice->status = 'sent';
        $invoice->sent_at = now();
        $invoice->save();
    }

    return back()->with('success', 'Invoice marked as sent.');

})->name('tenant.invoices.send');


        Route::post('invoices/{invoice}/email', function (\App\Models\Invoice $invoice) {

            if (!$invoice->brand->email) {
                return back()->with('error', 'Brand email not available.');
            }

            \Mail::to($invoice->brand->email)
                ->send(new \App\Mail\InvoiceMail($invoice));

            $invoice->status = 'sent';
            $invoice->sent_at = now();
            $invoice->save();

            return back()->with('success', 'Invoice sent successfully.');

        })->name('invoices.email');

    Route::post('invoices/{invoice}/remind', function (\App\Models\Invoice $invoice) {

    if (!$invoice->brand->email) {
        return back()->with('error', 'Brand email not available.');
    }

    \Mail::to($invoice->brand->email)
        ->send(new \App\Mail\InvoiceReminderMail($invoice));

    return back()->with('success', 'Reminder sent successfully.');

})->name('invoices.remind');
    
    
    Route::resource('recurring', RecurringInvoiceController::class)
    ->except(['show', 'edit', 'update']);

Route::post('recurring/{recurring}/toggle',
    [RecurringInvoiceController::class, 'toggle'])
    ->name('recurring.toggle');
    
    
    Route::resource('expenses', ExpenseController::class);
    
    Route::resource('payments', PaymentController::class);


Route::post('revenue/{split}/paid', function ($id) {

    $split = \App\Models\RevenueSplit::findOrFail($id);

    $split->status = 'paid';
    $split->paid_at = now();
    $split->save();

    return back()->with('success','Marked as paid.');

})->name('tenant.revenue.markPaid');

Route::get('team/revenue', function () {
    return view('tenant.team.revenue');
})->name('tenant.team.revenue');

    Route::get('/expenses', function () {
        $expenses = \App\Models\Expense::latest()->paginate(20);
        return view('tenant.expenses.index', compact('expenses'));
    })->name('expenses');


Route::get('reports', [ReportsController::class, 'index'])
    ->name('reports');

Route::get('reports/export', [ReportsController::class, 'exportCsv'])
    ->name('reports.export');
    
    Route::get('reports/pnl/pdf', [ReportsController::class, 'exportPnLPdf'])
->name('reports.pnl.pdf');

    Route::get('reports/gst', [ReportsController::class, 'gstSummary'])
    ->name('reports.gst');
    
    Route::get('reports/tax-summary', [ReportsController::class, 'gstSummary'])
    ->name('reports.tax-summary');
    // Route::get('/reports', function () {
    //     return view('tenant.reports.index');
    // })->name('reports');


Route::get('reports/gst/export', [ReportsController::class, 'exportGstCsv'])
    ->name('reports.gst.export');
    
    Route::get('tax', [ReportsController::class, 'simpleTaxSummary'])
    ->name('tax');
    // Route::get('/tax', function () {
    //     $taxSettings = \App\Models\TaxSetting::first();
    //     return view('tenant.tax.index', compact('taxSettings'));
    // })->name('tax');

Route::get('team', [TeamController::class, 'index'])->name('team');
Route::post('team/invite', [TeamController::class, 'invite'])->name('team.invite');
Route::post('team/{id}/remove', [TeamController::class, 'remove'])->name('team.remove');
Route::post('team/{id}/suspend', [TeamController::class, 'suspend'])->name('team.suspend');
Route::post('team/{id}/activate', [TeamController::class, 'activate'])->name('team.activate');
Route::post('team/{id}/resend', [TeamController::class, 'resend'])->name('team.resend');
Route::post('team/{id}/role', [TeamController::class, 'updateRole'])->name('team.role');
Route::get('team/{id}/profile', [\App\Http\Controllers\Tenant\TeamController::class, 'profile'])->name('team.profile');
Route::get('team/analytics', [\App\Http\Controllers\Tenant\TeamController::class, 'analytics'])->name('team.analytics');

Route::post('invoices/{invoice}/whatsapp', function (\App\Models\Invoice $invoice) {
    $brandPhone = preg_replace('/\D/', '', $invoice->brand->phone); // Only digits
    if (strlen($brandPhone) == 10) {
        $brandPhone = '91' . $brandPhone; // Add country code if missing
    }
    $link = url('/invoice/' . $invoice->public_token);
    $msg = urlencode("Hi, here is your invoice from " . config('app.name') . ": $link");

    // For demo: redirect to WhatsApp Web
    return redirect("https://wa.me/{$brandPhone}?text={$msg}");
})->name('invoices.whatsapp');



Route::get('billing', function () {
    $tenant = auth()->user()->tenant;
    return view('tenant.billing', compact('tenant'));
})->name('billing');

Route::get('billing/upgrade', function () {
    $tenant = auth()->user()->tenant;
    $razorpayPlanId = 'plan_JxXXXXXX'; // Your Razorpay Plan ID
    return view('tenant.billing-upgrade', compact('tenant', 'razorpayPlanId'));
})->name('billing.upgrade');

Route::get('billing/cancel', function () {
    // Show cancel confirmation or handle cancellation
    return view('tenant.billing-cancel');
})->name('billing.cancel');

// subscriptiom

Route::post('billing/create-subscription', function (\Illuminate\Http\Request $request) {
    $api = new \Razorpay\Api\Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
    $tenant = auth()->user()->tenant;

    $subscription = $api->subscription->create([
        'plan_id' => $request->plan_id,
        'customer_notify' => 1,
        'total_count' => 0, // Infinite
        'notes' => [
            'tenant_id' => $tenant->id,
            'email' => $tenant->owner->email,
        ]
    ]);

    // Optionally save subscription_id to tenant for tracking
    $tenant->razorpay_subscription_id = $subscription->id;
    $tenant->save();

    return response()->json(['subscription_id' => $subscription->id]);
});


Route::get('billing/success', function (\Illuminate\Http\Request $request) {
    // Mark tenant as pro, save payment_id, etc.
    $tenant = auth()->user()->tenant;
    $tenant->plan = 'pro';
    $tenant->status = 'active';
    $tenant->save();

    // Optionally create a subscription invoice record here

    return redirect()->route('billing')->with('success', 'Subscription activated!');
});

    Route::get('/settings', function () {
        $taxSettings = \App\Models\TaxSetting::first();
        return view('tenant.settings.index', compact('taxSettings'));
    })->name('settings');



});

Route::get('/invoice/{token}', function ($token) {

    $invoice = \App\Models\Invoice::where('public_token', $token)
        ->with(['brand', 'items'])
        ->firstOrFail();

    // Mark viewed
    if (!$invoice->viewed_at) {
        $invoice->viewed_at = now();
        $invoice->status = 'viewed';
        $invoice->save();
    }

    return view('public.invoice', compact('invoice'));

})->name('invoice.public');

// Fetch campaigns by brand
Route::get('/ajax/brand/{brand}/campaigns', function (\App\Models\Brand $brand) {
    return \App\Models\Campaign::where('brand_id', $brand->id)
        ->select('id', 'title')
        ->get();
});

// Fetch milestones by campaign
Route::get('/ajax/campaign/{campaign}/milestones', function (\App\Models\Campaign $campaign) {
    return \App\Models\Milestone::where('campaign_id', $campaign->id)
        ->where('status', 'completed')
        ->whereNull('invoice_id')
        ->select('id', 'title', 'amount')
        ->get();
});

Route::get('/accept-invite/{token}', [\App\Http\Controllers\Auth\AcceptInviteController::class, 'showForm'])->name('accept-invite');
Route::post('/accept-invite/{token}', [\App\Http\Controllers\Auth\AcceptInviteController::class, 'accept']);

Route::post('/webhook/razorpay', function (\Illuminate\Http\Request $request) {
    $payload = $request->all();

    // Downgrade on cancel/halt
    if (in_array($payload['event'], ['subscription.cancelled', 'subscription.halted'])) {
        $subscriptionId = $payload['payload']['subscription']['entity']['id'];
        $tenant = \App\Models\Tenant::where('razorpay_subscription_id', $subscriptionId)->first();
        if ($tenant) {
            $tenant->plan = 'free';
            $tenant->status = 'suspended';
            $tenant->save();
        }
    }

    // Create invoice on payment success
    if ($payload['event'] == 'invoice.paid') {
        $subscriptionId = $payload['payload']['invoice']['entity']['subscription_id'];
        $tenant = \App\Models\Tenant::where('razorpay_subscription_id', $subscriptionId)->first();
        if ($tenant) {
            \App\Models\Invoice::create([
                'tenant_id' => $tenant->id,
                'brand_id' => null,
                'invoice_number' => 'SUB-' . now()->format('Ym') . '-' . rand(1000,9999),
                'issue_date' => now(),
                'due_date' => now(),
                'subtotal' => $payload['payload']['invoice']['entity']['amount_paid'] / 100,
                'taxable_amount' => $payload['payload']['invoice']['entity']['amount_paid'] / 100,
                'total_amount' => $payload['payload']['invoice']['entity']['amount_paid'] / 100,
                'amount_paid' => $payload['payload']['invoice']['entity']['amount_paid'] / 100,
                'balance_due' => 0,
                'currency' => 'INR',
                'status' => 'paid',
                'notes' => 'AdivoQ Pro Subscription',
            ]);
        }
    }

    return response('OK', 200);
});