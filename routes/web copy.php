<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| Public Website Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [App\Http\Controllers\Public\HomeController::class, 'index'])->name('home');
Route::get('/blog', [App\Http\Controllers\Public\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [App\Http\Controllers\Public\BlogController::class, 'show'])->name('blog.show');
Route::get('/guides', [App\Http\Controllers\Public\GuideController::class, 'index'])->name('guides.index');
Route::get('/guides/{slug}', [App\Http\Controllers\Public\GuideController::class, 'show'])->name('guides.show');
Route::get('/roadmap', [App\Http\Controllers\Public\RoadmapController::class, 'index'])->name('roadmap');
Route::post('/roadmap/{item}/vote', [App\Http\Controllers\Public\RoadmapController::class, 'vote'])->name('roadmap.vote');
Route::get('/tax-calculator', [App\Http\Controllers\Public\ToolController::class, 'taxCalculator'])->name('tools.tax-calculator');
Route::post('/tax-calculator', [App\Http\Controllers\Public\ToolController::class, 'calculateTax'])->name('tools.calculate-tax');
Route::get('/invoice-generator', [App\Http\Controllers\Public\ToolController::class, 'invoiceGenerator'])->name('tools.invoice-generator');
Route::post('/invoice-generator/pdf', [App\Http\Controllers\Public\ToolController::class, 'generateFreePdf'])->name('tools.generate-pdf');
Route::get('/templates', [App\Http\Controllers\Public\ToolController::class, 'templates'])->name('tools.templates');
Route::get('/contact', [App\Http\Controllers\Public\ContactController::class, 'index'])->name('contact');
Route::post('/contact', [App\Http\Controllers\Public\ContactController::class, 'store'])->name('contact.store');
Route::post('/waitlist', [App\Http\Controllers\Public\WaitlistController::class, 'store'])->name('waitlist.store');
Route::get('/privacy-policy', fn () => view('public.privacy'))->name('privacy');
Route::get('/terms-of-service', fn () => view('public.terms'))->name('terms');
Route::get('/refund-policy', fn () => view('public.refund'))->name('refund');
Route::post('/webhooks/razorpay', [App\Http\Controllers\Public\RazorpayWebhookController::class, 'handle'])
    ->name('webhooks.razorpay');
    Route::post('/webhooks/razorpay/platform', [App\Http\Controllers\Public\RazorpayPlatformWebhookController::class, 'handle'])
    ->name('webhooks.razorpay.platform');
/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Team invitation acceptance
Route::get('/invitation/{token}', [App\Http\Controllers\Auth\InvitationController::class, 'show'])->name('invitation.show');
Route::post('/invitation/{token}', [App\Http\Controllers\Auth\InvitationController::class, 'accept'])->name('invitation.accept');

// Payment link (public)
Route::get('/pay/{token}', [App\Http\Controllers\Public\PaymentLinkController::class, 'show'])->name('payment.link');
Route::post('/pay/{token}/process', [App\Http\Controllers\Public\PaymentLinkController::class, 'process'])->name('payment.link.process');

Route::get('/pay/{token}/result', [App\Http\Controllers\Public\PaymentLinkController::class, 'result'])
    ->name('payment.link.result');

Route::post('/pay/{token}/razorpay/order', [App\Http\Controllers\Public\PaymentLinkController::class, 'razorpayCreateOrder'])
    ->name('payment.link.razorpay.order');

Route::post('/pay/{token}/razorpay/verify', [App\Http\Controllers\Public\PaymentLinkController::class, 'razorpayVerifyPayment'])
    ->name('payment.link.razorpay.verify');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');


    Route::middleware('admin')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        
        // Tenant Management
        Route::resource('tenants', App\Http\Controllers\Admin\TenantController::class);
        Route::post('tenants/{tenant}/suspend', [App\Http\Controllers\Admin\TenantController::class, 'suspend'])->name('tenants.suspend');
        Route::post('tenants/{tenant}/reactivate', [App\Http\Controllers\Admin\TenantController::class, 'reactivate'])->name('tenants.reactivate');
        Route::get('tenants/{tenant}/impersonate', [App\Http\Controllers\Admin\TenantController::class, 'impersonate'])->name('tenants.impersonate');
    Route::post('/stop-impersonation', [App\Http\Controllers\Admin\ImpersonationController::class, 'stop'])
    ->name('stop-impersonation');
        // Blog
        Route::resource('blog-categories', App\Http\Controllers\Admin\BlogCategoryController::class);
        Route::resource('blog-posts', App\Http\Controllers\Admin\BlogPostController::class);

        // Guides
        Route::resource('guides', App\Http\Controllers\Admin\GuideController::class);
        Route::post('guides/{guide}/steps', [App\Http\Controllers\Admin\GuideController::class, 'storeStep'])->name('guides.steps.store');
        Route::put('guides/{guide}/steps/{step}', [App\Http\Controllers\Admin\GuideController::class, 'updateStep'])->name('guides.steps.update');
        Route::delete('guides/{guide}/steps/{step}', [App\Http\Controllers\Admin\GuideController::class, 'destroyStep'])->name('guides.steps.destroy');

        // Roadmap
        Route::resource('roadmap', App\Http\Controllers\Admin\RoadmapController::class);

        // Waitlist
        Route::get('waitlist', [App\Http\Controllers\Admin\WaitlistController::class, 'index'])->name('waitlist.index');
        Route::get('waitlist/export', [App\Http\Controllers\Admin\WaitlistController::class, 'export'])->name('waitlist.export');
        Route::post('waitlist/invite', [App\Http\Controllers\Admin\WaitlistController::class, 'sendInvites'])->name('waitlist.invite');

        // Contact Messages
        Route::get('messages', [App\Http\Controllers\Admin\MessageController::class, 'index'])->name('messages.index');
        Route::get('messages/{message}', [App\Http\Controllers\Admin\MessageController::class, 'show'])->name('messages.show');
        Route::put('messages/{message}', [App\Http\Controllers\Admin\MessageController::class, 'update'])->name('messages.update');

        // Settings
        Route::get('settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

        // Admin Users
        Route::resource('admin-users', App\Http\Controllers\Admin\AdminUserController::class);

        // Activity Logs
        Route::get('activity-logs', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('activity-logs/export', [App\Http\Controllers\Admin\ActivityLogController::class, 'export'])->name('activity-logs.export');

        // Analytics
        Route::get('analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    });
});

/*
|--------------------------------------------------------------------------
| Tenant Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::prefix('dashboard')->name('dashboard.')->middleware(['auth', 'tenant'])->group(function () {
    // C1. Dashboard Home
    Route::get('/', [App\Http\Controllers\Tenant\DashboardController::class, 'index'])->name('home');
Route::resource('tds-certificates', \App\Http\Controllers\Tenant\TdsCertificateController::class);


    // C2. Brand Management
    Route::resource('brands', App\Http\Controllers\Tenant\BrandController::class);
    Route::post('brands/{brand}/archive', [App\Http\Controllers\Tenant\BrandController::class, 'archive'])->name('brands.archive');
    Route::post('brands/{brand}/restore', [App\Http\Controllers\Tenant\BrandController::class, 'restore'])->name('brands.restore');

    // C3. Campaign Management
    Route::resource('campaigns', App\Http\Controllers\Tenant\CampaignController::class);
    Route::post('campaigns/{campaign}/complete', [App\Http\Controllers\Tenant\CampaignController::class, 'complete'])->name('campaigns.complete');
    Route::post('campaigns/{campaign}/cancel', [App\Http\Controllers\Tenant\CampaignController::class, 'cancel'])->name('campaigns.cancel');

    // C4. Milestones (nested under campaigns)
    Route::post('campaigns/{campaign}/milestones', [App\Http\Controllers\Tenant\MilestoneController::class, 'store'])->name('milestones.store');
    Route::put('milestones/{milestone}', [App\Http\Controllers\Tenant\MilestoneController::class, 'update'])->name('milestones.update');
    Route::delete('milestones/{milestone}', [App\Http\Controllers\Tenant\MilestoneController::class, 'destroy'])->name('milestones.destroy');
    Route::post('milestones/{milestone}/complete', [App\Http\Controllers\Tenant\MilestoneController::class, 'complete'])->name('milestones.complete');
    Route::post('milestones/reorder', [App\Http\Controllers\Tenant\MilestoneController::class, 'reorder'])->name('milestones.reorder');

    // C5. Invoice Management ⭐
    Route::resource('invoices', App\Http\Controllers\Tenant\InvoiceController::class);
    Route::post('invoices/{invoice}/send-email', [App\Http\Controllers\Tenant\InvoiceController::class, 'sendEmail'])->name('invoices.send-email');
    Route::post('invoices/{invoice}/send-whatsapp', [App\Http\Controllers\Tenant\InvoiceController::class, 'sendWhatsApp'])->name('invoices.send-whatsapp');
    Route::post('invoices/{invoice}/payment-link', [App\Http\Controllers\Tenant\InvoiceController::class, 'generatePaymentLink'])->name('invoices.payment-link');
    Route::get('invoices/{invoice}/pdf', [App\Http\Controllers\Tenant\InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::post('invoices/{invoice}/record-payment', [App\Http\Controllers\Tenant\InvoiceController::class, 'recordPayment'])->name('invoices.record-payment');
    Route::post('invoices/{invoice}/send-reminder', [App\Http\Controllers\Tenant\InvoiceController::class, 'sendReminder'])->name('invoices.send-reminder');
    Route::post('invoices/{invoice}/duplicate', [App\Http\Controllers\Tenant\InvoiceController::class, 'duplicate'])->name('invoices.duplicate');
    Route::post('invoices/{invoice}/cancel', [App\Http\Controllers\Tenant\InvoiceController::class, 'cancel'])->name('invoices.cancel');

    Route::get('invoices/{invoice}/recurring', [App\Http\Controllers\Tenant\RecurringInvoiceController::class, 'edit'])
    ->name('invoices.recurring.edit');

Route::put('invoices/{invoice}/recurring', [App\Http\Controllers\Tenant\RecurringInvoiceController::class, 'update'])
    ->name('invoices.recurring.update');

Route::delete('invoices/{invoice}/recurring', [App\Http\Controllers\Tenant\RecurringInvoiceController::class, 'destroy'])
    ->name('invoices.recurring.destroy');

    // C6. Payment Management
    Route::resource('payments', App\Http\Controllers\Tenant\PaymentController::class)->only(['index', 'show']);

    Route::get('billing', [App\Http\Controllers\Tenant\BillingController::class, 'index'])->name('billing.index');
Route::post('billing/razorpay/create', [App\Http\Controllers\Tenant\BillingController::class, 'createSubscription'])->name('billing.razorpay.create');
Route::post('billing/razorpay/verify', [App\Http\Controllers\Tenant\BillingController::class, 'verifySubscription'])->name('billing.razorpay.verify');
Route::get('billing/result', [App\Http\Controllers\Tenant\BillingController::class, 'result'])->name('billing.result');
Route::post('billing/cancel', [App\Http\Controllers\Tenant\BillingController::class, 'cancel'])->name('billing.cancel');

    // C7. Expense Tracking
    Route::resource('expenses', App\Http\Controllers\Tenant\ExpenseController::class);
    Route::resource('expense-categories', App\Http\Controllers\Tenant\ExpenseCategoryController::class)->only(['index', 'store', 'update', 'destroy']);

    // C8. Financial Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\Tenant\ReportController::class, 'index'])->name('index');
        Route::get('/revenue', [App\Http\Controllers\Tenant\ReportController::class, 'revenue'])->name('revenue');
        Route::get('/invoice-aging', [App\Http\Controllers\Tenant\ReportController::class, 'invoiceAging'])->name('invoice-aging');
        Route::get('/payment-collection', [App\Http\Controllers\Tenant\ReportController::class, 'paymentCollection'])->name('payment-collection');
        Route::get('/expenses', [App\Http\Controllers\Tenant\ReportController::class, 'expenses'])->name('expenses');
        Route::get('/profit-loss', [App\Http\Controllers\Tenant\ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/tax-summary', [App\Http\Controllers\Tenant\ReportController::class, 'taxSummary'])->name('tax-summary');
        Route::get('/export/{type}', [App\Http\Controllers\Tenant\ReportController::class, 'export'])->name('export');
        Route::get('/pdf/{type}', [App\Http\Controllers\Tenant\ReportController::class, 'pdf'])->name('pdf');

    });

    // C9. Tax Management
    Route::get('tax', [App\Http\Controllers\Tenant\TaxController::class, 'index'])->name('tax.index');
    Route::put('tax', [App\Http\Controllers\Tenant\TaxController::class, 'update'])->name('tax.update');

        // ✅ GST Returns (NEW)
    Route::get('tax/returns', [App\Http\Controllers\Tenant\GstReturnController::class, 'index'])->name('tax.returns');
    Route::get('tax/returns/export/{type}', [App\Http\Controllers\Tenant\GstReturnController::class, 'export'])->name('tax.returns.export');

    // C10. Team Management
    Route::middleware('role:owner,manager')->group(function () {
        Route::get('team', [App\Http\Controllers\Tenant\TeamController::class, 'index'])->name('team.index');
        Route::post('team/invite', [App\Http\Controllers\Tenant\TeamController::class, 'invite'])->name('team.invite');
        Route::put('team/{user}/role', [App\Http\Controllers\Tenant\TeamController::class, 'updateRole'])->name('team.update-role');
        Route::post('team/{user}/suspend', [App\Http\Controllers\Tenant\TeamController::class, 'suspend'])->name('team.suspend');
        Route::post('team/{user}/reactivate', [App\Http\Controllers\Tenant\TeamController::class, 'reactivate'])->name('team.reactivate');
        Route::delete('team/{user}', [App\Http\Controllers\Tenant\TeamController::class, 'remove'])->name('team.remove');
        Route::delete('team/invitation/{invitation}', [App\Http\Controllers\Tenant\TeamController::class, 'cancelInvitation'])->name('team.cancel-invitation');
    });

    // C11. Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [App\Http\Controllers\Tenant\SettingController::class, 'index'])->name('index');
        Route::put('/profile', [App\Http\Controllers\Tenant\SettingController::class, 'updateProfile'])->name('profile');
        Route::put('/invoice', [App\Http\Controllers\Tenant\SettingController::class, 'updateInvoiceSettings'])->name('invoice');
        Route::put('/bank', [App\Http\Controllers\Tenant\SettingController::class, 'updateBankDetails'])->name('bank');
        Route::put('/notifications', [App\Http\Controllers\Tenant\SettingController::class, 'updateNotifications'])->name('notifications');
        Route::put('/payment-gateway', [App\Http\Controllers\Tenant\SettingController::class, 'updatePaymentGateway'])->name('payment-gateway');
        Route::put('/password', [App\Http\Controllers\Tenant\SettingController::class, 'updatePassword'])->name('password');
        Route::get('/export/{type}', [App\Http\Controllers\Tenant\SettingController::class, 'export'])->name('export');
    });

    // AJAX endpoints
    Route::get('api/brands/search', [App\Http\Controllers\Tenant\BrandController::class, 'search'])->name('brands.search');
    Route::get('api/brands/{brand}/details', [App\Http\Controllers\Tenant\BrandController::class, 'details'])->name('brands.details');
});