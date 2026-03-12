<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\BlogController;

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
        $stats = [
            'revenue' => \App\Models\Payment::sum('amount'),
            'invoices' => \App\Models\Invoice::count(),
            'brands' => \App\Models\Brand::count(),
            'pending' => \App\Models\Invoice::whereNotIn('status', ['paid', 'cancelled'])->sum('balance_due'),
        ];
        $recentInvoices = \App\Models\Invoice::with('brand')->latest()->take(5)->get();
        return view('tenant.dashboard', compact('stats', 'recentInvoices'));
    })->name('dashboard');

    Route::get('/brands', function () {
        $brands = \App\Models\Brand::withCount('invoices')
            ->withSum('payments as total_revenue', 'amount')
            ->latest()
            ->paginate(20);
        return view('tenant.brands.index', compact('brands'));
    })->name('brands');

    Route::get('/campaigns', function () {
        $campaigns = \App\Models\Campaign::with('brand')->latest()->paginate(20);
        return view('tenant.campaigns.index', compact('campaigns'));
    })->name('campaigns');

    Route::get('/invoices', function () {
        $invoices = \App\Models\Invoice::with('brand')->latest()->paginate(20);
        return view('tenant.invoices.index', compact('invoices'));
    })->name('invoices');

    Route::get('/invoices/create', function () {
        $brands = \App\Models\Brand::where('status', 'active')->orderBy('name')->get();
        $campaigns = \App\Models\Campaign::where('status', 'active')->orderBy('title')->get();
        return view('tenant.invoices.create', compact('brands', 'campaigns'));
    })->name('invoices.create');

    Route::get('/payments', function () {
        $payments = \App\Models\Payment::with(['brand', 'invoice'])->latest()->paginate(20);
        return view('tenant.payments.index', compact('payments'));
    })->name('payments');

    Route::get('/expenses', function () {
        $expenses = \App\Models\Expense::latest()->paginate(20);
        return view('tenant.expenses.index', compact('expenses'));
    })->name('expenses');

    Route::get('/reports', function () {
        return view('tenant.reports.index');
    })->name('reports');

    Route::get('/tax', function () {
        $taxSettings = \App\Models\TaxSetting::first();
        return view('tenant.tax.index', compact('taxSettings'));
    })->name('tax');

    Route::get('/team', function () {
        $members = \App\Models\TeamMember::with('user')->get();
        return view('tenant.team.index', compact('members'));
    })->name('team');

    Route::get('/settings', function () {
        $taxSettings = \App\Models\TaxSetting::first();
        return view('tenant.settings.index', compact('taxSettings'));
    })->name('settings');
});