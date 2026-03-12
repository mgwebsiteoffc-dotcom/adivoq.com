<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('public.landing');
})->name('home');

Route::get('/blog', function () {
    $posts = \App\Models\BlogPost::where('status', 'published')
        ->orderBy('published_at', 'desc')
        ->paginate(10);
    return view('public.blog.index', compact('posts'));
})->name('blog');

Route::get('/blog/{slug}', function ($slug) {
    $post = \App\Models\BlogPost::where('slug', $slug)->where('status', 'published')->firstOrFail();
    $post->increment('views_count');
    return view('public.blog.show', compact('post'));
})->name('blog.show');

Route::get('/roadmap', function () {
    $items = \App\Models\RoadmapItem::orderByRaw("FIELD(status, 'in_progress', 'planned', 'completed')")
        ->orderBy('priority', 'desc')
        ->get();
    return view('public.roadmap', compact('items'));
})->name('roadmap');

Route::get('/contact', function () {
    return view('public.contact');
})->name('contact');

Route::post('/contact', function () {
    request()->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'message' => 'required|string|max:2000',
    ]);

    \App\Models\ContactMessage::create([
        'name' => request('name'),
        'email' => request('email'),
        'subject' => request('subject'),
        'message' => request('message'),
    ]);

    return back()->with('success', 'Thank you! Your message has been sent.');
})->name('contact.submit');

Route::post('/waitlist', function () {
    request()->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:waitlist,email',
    ]);

    \App\Models\Waitlist::create([
        'name' => request('name'),
        'email' => request('email'),
        'creator_type' => request('creator_type'),
        'source' => 'landing',
    ]);

    return back()->with('success', 'You\'re on the list! We\'ll notify you when we launch.');
})->name('waitlist.submit');

Route::get('/tools/tax-calculator', function () {
    return view('public.tools.tax-calculator');
})->name('tools.tax-calculator');

Route::get('/tools/invoice-generator', function () {
    return view('public.tools.invoice-generator');
})->name('tools.invoice-generator');

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

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Placeholder routes - we'll build these in Phase 2
    Route::get('/tenants', function () {
        $tenants = \App\Models\Tenant::with('owner')->latest()->paginate(20);
        return view('admin.tenants.index', compact('tenants'));
    })->name('admin.tenants');

    Route::get('/blog', function () {
        $posts = \App\Models\BlogPost::with('author')->latest()->paginate(20);
        return view('admin.blog.index', compact('posts'));
    })->name('admin.blog');

    Route::get('/roadmap', function () {
        $items = \App\Models\RoadmapItem::latest()->paginate(20);
        return view('admin.roadmap.index', compact('items'));
    })->name('admin.roadmap');

    Route::get('/waitlist', function () {
        $entries = \App\Models\Waitlist::latest()->paginate(20);
        return view('admin.waitlist.index', compact('entries'));
    })->name('admin.waitlist');

    Route::get('/messages', function () {
        $messages = \App\Models\ContactMessage::latest()->paginate(20);
        return view('admin.messages.index', compact('messages'));
    })->name('admin.messages');

    Route::get('/users', function () {
        $users = \App\Models\User::where('is_system_admin', true)->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    })->name('admin.users');

    Route::get('/settings', function () {
        return view('admin.settings');
    })->name('admin.settings');

    Route::get('/logs', function () {
        $logs = \App\Models\ActivityLog::with('user')->latest()->paginate(50);
        return view('admin.logs.index', compact('logs'));
    })->name('admin.logs');
});

/*
|--------------------------------------------------------------------------
| Tenant (Creator Dashboard) Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'tenant'])->prefix('dashboard')->group(function () {
    Route::get('/', function () {
        return view('tenant.dashboard');
    })->name('dashboard');

    // Placeholder routes - we'll build these in Phase 3-4
    Route::get('/brands', function () {
        $brands = \App\Models\Brand::latest()->paginate(20);
        return view('tenant.brands.index', compact('brands'));
    })->name('tenant.brands');

    Route::get('/campaigns', function () {
        $campaigns = \App\Models\Campaign::with('brand')->latest()->paginate(20);
        return view('tenant.campaigns.index', compact('campaigns'));
    })->name('tenant.campaigns');

    Route::get('/invoices', function () {
        $invoices = \App\Models\Invoice::with('brand')->latest()->paginate(20);
        return view('tenant.invoices.index', compact('invoices'));
    })->name('tenant.invoices');

    Route::get('/invoices/create', function () {
        $brands = \App\Models\Brand::where('status', 'active')->get();
        return view('tenant.invoices.create', compact('brands'));
    })->name('tenant.invoices.create');

    Route::get('/payments', function () {
        $payments = \App\Models\Payment::with(['brand', 'invoice'])->latest()->paginate(20);
        return view('tenant.payments.index', compact('payments'));
    })->name('tenant.payments');

    Route::get('/expenses', function () {
        $expenses = \App\Models\Expense::latest()->paginate(20);
        return view('tenant.expenses.index', compact('expenses'));
    })->name('tenant.expenses');

    Route::get('/reports', function () {
        return view('tenant.reports.index');
    })->name('tenant.reports');

    Route::get('/tax', function () {
        return view('tenant.tax.index');
    })->name('tenant.tax');

    Route::get('/team', function () {
        return view('tenant.team.index');
    })->name('tenant.team');

    Route::get('/settings', function () {
        return view('tenant.settings.index');
    })->name('tenant.settings');
});