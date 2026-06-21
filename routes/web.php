<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Support\LaravelRequest as Request;


use App\Http\Controllers\Auth\CitizenRegistrationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\TotpSetupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordResetCodeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\CitizenRequestController;
use App\Http\Controllers\CitizenRequestRatingController;
use App\Http\Controllers\CryptoPaymentController;
use App\Http\Controllers\PaymentController;

// Admin
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\OfficeController as AdminOfficeController;
use App\Http\Controllers\Admin\MunicipalityController as AdminMunicipalityController;
use App\Http\Controllers\Admin\ServiceRequestController as AdminServiceRequestController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;

// Staff
use App\Http\Controllers\Staff\ServiceController;

// Citizen
use App\Http\Controllers\Citizen\ServiceApplicationController;

// ==========================================
// PUBLIC ROUTES
// ==========================================

Route::get('/', function () {
    return View::make('welcome');
})->name('welcome');

// Public QR tracking — no login required
Route::get('/track/{uuid}', [TrackingController::class, 'index'])->name('track.show');

// Language switcher
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, \App\Http\Middleware\SetLocale::SUPPORTED, true)) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

Route::get('/services', [ServiceApplicationController::class, 'browse'])->name('citizen.services.browse');


// ==========================================
// AUTHENTICATION
// ==========================================
Route::get('/login',   [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login',  [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register',  [CitizenRegistrationController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [CitizenRegistrationController::class, 'register'])->name('register.store');

// Google OAuth
Route::get('/auth/google',          [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

// Forgot Password (4-digit code)
Route::get('/forgot-password',      [PasswordResetCodeController::class, 'showEmailForm'])->name('password.request');
Route::post('/forgot-password',     [PasswordResetCodeController::class, 'sendCode'])->name('password.code.send');
Route::get('/verify-code',          [PasswordResetCodeController::class, 'showVerifyForm'])->name('password.code.verify.form');
Route::post('/verify-code',         [PasswordResetCodeController::class, 'verifyCode'])->name('password.code.verify');
Route::get('/reset-password-code',  [PasswordResetCodeController::class, 'showResetForm'])->name('password.code.reset.form');
Route::post('/reset-password-code', [PasswordResetCodeController::class, 'resetPassword'])->name('password.code.reset');

// 2FA Challenge (pre-auth)
Route::get('/2fa/challenge', [TwoFactorController::class, 'showChallenge'])->name('2fa.challenge');
Route::post('/2fa/verify',   [TwoFactorController::class, 'verify'])->name('2fa.verify');
Route::post('/2fa/resend',   [TwoFactorController::class, 'resend'])->name('2fa.resend');

// First-login forced password change (staff logged in with their emailed temp password)
Route::middleware(['auth'])->group(function () {
    Route::get('/first-login/set-password',  [\App\Http\Controllers\Auth\FirstLoginOtpController::class, 'showSetPassword'])->name('first-login.set-password');
    Route::post('/first-login/set-password', [\App\Http\Controllers\Auth\FirstLoginOtpController::class, 'setPassword'])->name('first-login.set-password.store');
});


// ==========================================
// AUTHENTICATED ROUTES
// ==========================================
Route::middleware(['auth'])->group(function () {

    // Notifications
    Route::get('/notifications',               [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all',     [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::post('/notifications/{id}/read',    [NotificationController::class, 'markRead'])->name('notifications.read');

    // Home & Dashboard
    Route::get('/home',      [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', fn() => View::make('dashboard'))->name('dashboard');

    // Profile
    Route::get('/profile',            [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile',           [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile',          [ProfileController::class, 'update'])->name('profile.update.patch');
    Route::delete('/profile',         [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // 2FA Setup
    Route::get('/2fa/setup',    [TotpSetupController::class, 'setup'])->name('2fa.setup');
    Route::post('/2fa/confirm', [TotpSetupController::class, 'confirm'])->name('2fa.confirm');

    // Citizen: Home & Apply
    Route::get('/citizen/home',  [CitizenRequestController::class, 'index'])->name('citizen.home');
    Route::get('/apply',         [CitizenRequestController::class, 'create'])->name('citizen.apply');
    Route::post('/apply',        [CitizenRequestController::class, 'store'])->name('citizen.apply.store');
    Route::get('/my-requests',   [CitizenRequestController::class, 'myRequests'])->name('citizen.my-requests');
    Route::get('/how-it-works',  [HomeController::class, 'howItWorks'])->name('citizen.how-it-works');

    // Citizen: Role-protected requests
    Route::middleware(['role:citizen'])->prefix('my')->name('citizen.')->group(function () {
        Route::get('/requests',            [ServiceRequestController::class, 'index'])->name('requests.index');
        Route::get('/requests/new',        [ServiceRequestController::class, 'create'])->name('requests.create');
        Route::post('/requests',           [ServiceRequestController::class, 'store'])->name('requests.store');
        Route::post('/requests/{id}/rate', [RatingController::class, 'store'])->name('service-requests.rate');
    });

    // Request detail
    Route::get('/requests/{uuid}', [ServiceRequestController::class, 'show'])->name('citizen.requests.show');

    // Service Applications
    Route::get('/services/{service}/apply',           [ServiceApplicationController::class, 'apply'])->name('citizen.services.apply');
    Route::post('/services/{service}/apply',          [ServiceApplicationController::class, 'store'])->name('citizen.services.store');
    Route::get('/applications/{application}/success', [ServiceApplicationController::class, 'success'])->name('citizen.applications.success');
    Route::get('/my-applications',                    [ServiceApplicationController::class, 'myApplications'])->name('citizen.applications.index');
    Route::get('/requests/{citizenRequest}/payment',  [ServiceApplicationController::class, 'selectPayment'])->name('citizen.payment.select');

    // Chat
    Route::post('/chat/{id}/send',    [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/{id}/messages', [ChatController::class, 'getMessages'])->name('chat.messages');

    // Stripe Payments (legacy ServiceRequest flow)
    Route::post('/payment/checkout/{id}', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/success/{id}',   [PaymentController::class, 'success'])->name('payment.success');

    // Stripe Payments (CitizenRequest / service application flow)
    Route::post('/requests/{citizenRequest}/stripe-checkout', [PaymentController::class, 'checkoutCitizenRequest'])->name('stripe.citizen.checkout');
    Route::get('/requests/{citizenRequest}/stripe-success',   [PaymentController::class, 'successCitizenRequest'])->name('stripe.citizen.success');

    // Crypto Payments
    Route::get('/requests/{citizenRequest}/crypto-payment',  [CryptoPaymentController::class, 'show'])->name('crypto.payment');
    Route::post('/requests/{citizenRequest}/crypto-payment', [CryptoPaymentController::class, 'initiate'])->name('crypto.initiate');
    Route::post('/crypto-transactions/{transaction}/submit', [CryptoPaymentController::class, 'submitTxHash'])->name('crypto.submit-tx');

    // Certificate Download
    Route::get('/requests/{citizenRequest}/certificate', [CitizenRequestController::class, 'downloadCertificate'])->name('requests.certificate');

    // Decrypt and stream an uploaded supporting document (owner, assigned office staff, or admin only)
    Route::get('/requests/{citizenRequest}/documents/{index}', [CitizenRequestController::class, 'serveDocument'])->name('requests.document');

    // Citizen: Rate a completed request
    Route::post('/requests/{citizenRequest}/rate', [CitizenRequestRatingController::class, 'store'])->name('citizen.requests.rate');

    // Citizen: Resubmit a rejected request
    Route::get('/requests/{citizenRequest}/resubmit',  [CitizenRequestController::class, 'resubmit'])->name('citizen.requests.resubmit');
    Route::post('/requests/{citizenRequest}/resubmit', [CitizenRequestController::class, 'resubmitStore'])->name('citizen.requests.resubmit.store');

    // Payment receipt PDF
    Route::get('/requests/{citizenRequest}/receipt', [CitizenRequestController::class, 'paymentReceipt'])->name('requests.payment-receipt');

    // Citizen: Chat with office per request
    Route::get('/requests/{citizenRequest}/chat',     [\App\Http\Controllers\CitizenRequestMessageController::class, 'citizenShow'])->name('citizen.requests.chat');
    Route::post('/requests/{citizenRequest}/chat',    [\App\Http\Controllers\CitizenRequestMessageController::class, 'citizenSend'])->name('citizen.requests.chat.send');
    Route::get('/requests/{citizenRequest}/messages', [\App\Http\Controllers\CitizenRequestMessageController::class, 'citizenMessages'])->name('citizen.requests.messages');

    // Citizen: Appointments
    Route::get('/appointments',                                         [\App\Http\Controllers\AppointmentController::class, 'citizenIndex'])->name('citizen.appointments.index');
    Route::post('/appointments/{appointment}/confirm',                  [\App\Http\Controllers\AppointmentController::class, 'citizenConfirm'])->name('citizen.appointments.confirm');
    Route::post('/appointments/{appointment}/cancel',                   [\App\Http\Controllers\AppointmentController::class, 'citizenCancel'])->name('citizen.appointments.cancel');
});


// ==========================================
// ADMIN & STAFF ROUTES
// ==========================================

Route::middleware(['auth', 'role:admin,office'])->prefix('office')->group(function () {
    Route::get('/dashboard', function (Request $request) {
        $user  = Auth::user();
        $query = \App\Models\CitizenRequest::with(['user', 'service', 'office', 'localPayments', 'cryptoTransactions'])
            ->latest();

        // Office staff only see requests for their own office
        if ($user->role === 'office' && $user->office_id) {
            $query->where('office_id', $user->office_id);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u
                      ->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name',  'like', "%{$search}%")
                      ->orWhere('email',      'like', "%{$search}%"))
                  ->orWhereHas('service', fn($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $requests = $query->get();

        $base = $user->role === 'office' && $user->office_id
            ? \App\Models\CitizenRequest::where('office_id', $user->office_id)
            : \App\Models\CitizenRequest::query();

        $stats = [
            'pending'   => (clone $base)->whereIn('status', ['pending', 'pending_payment'])->count(),
            'in_review' => (clone $base)->where('status', 'in_review')->count(),
            'approved'  => (clone $base)->where('status', 'approved')->count(),
        ];

        return view('office.dashboard', compact('requests', 'stats'));
    })->name('office.dashboard');

    Route::patch('/requests/{citizenRequest}/status',           [CitizenRequestController::class, 'officeUpdateStatus'])->name('office.requests.status');
    Route::post('/requests/{citizenRequest}/upload-response',   [CitizenRequestController::class, 'uploadResponse'])->name('office.requests.upload-response');

    // Ratings
    Route::get('/ratings',                                      [CitizenRequestRatingController::class, 'officeIndex'])->name('office.ratings.index');
    Route::post('/ratings/{rating}/respond',                    [CitizenRequestRatingController::class, 'respond'])->name('office.ratings.respond');

    // Appointments
    Route::get('/appointments',                                 [\App\Http\Controllers\AppointmentController::class, 'officeIndex'])->name('office.appointments.index');
    Route::post('/appointments',                                [\App\Http\Controllers\AppointmentController::class, 'store'])->name('office.appointments.store');
    Route::patch('/appointments/{appointment}/status',          [\App\Http\Controllers\AppointmentController::class, 'updateStatus'])->name('office.appointments.status');
    Route::delete('/appointments/{appointment}',                [\App\Http\Controllers\AppointmentController::class, 'destroy'])->name('office.appointments.destroy');

    // Chat per CitizenRequest
    Route::get('/requests/{citizenRequest}/chat',               [\App\Http\Controllers\CitizenRequestMessageController::class, 'show'])->name('office.requests.chat');
    Route::post('/requests/{citizenRequest}/chat',              [\App\Http\Controllers\CitizenRequestMessageController::class, 'store'])->name('office.requests.chat.send');
    Route::get('/requests/{citizenRequest}/messages',           [\App\Http\Controllers\CitizenRequestMessageController::class, 'officeMessages'])->name('office.requests.messages');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // User management
    Route::get('/users',              [AdminUserController::class, 'index'])->name('admin.users');
    Route::post('/users',             [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::patch('/users/{id}/role',  [AdminUserController::class, 'updateRole'])->name('admin.users.role');
    Route::delete('/users/{id}',      [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    // Staff management
    Route::get('/staff/create',    [AdminController::class, 'create'])->name('admin.staff.create');
    Route::post('/staff',          [AdminController::class, 'store'])->name('admin.staff.store');
    Route::get('/staff/{id}/edit', [AdminController::class, 'edit'])->name('admin.staff.edit');
    Route::put('/staff/{id}',      [AdminController::class, 'update'])->name('admin.staff.update');
    Route::delete('/staff/{id}',   [AdminController::class, 'destroy'])->name('admin.staff.destroy');

    // Request review
    Route::get('/requests',        [AdminServiceRequestController::class, 'index'])->name('admin.requests.index');
    Route::patch('/requests/{id}', [AdminServiceRequestController::class, 'update'])->name('admin.requests.update');

    // Municipality management
    Route::get('/municipalities',              [AdminMunicipalityController::class, 'index'])->name('admin.municipalities.index');
    Route::post('/municipalities',             [AdminMunicipalityController::class, 'store'])->name('admin.municipalities.store');
    Route::put('/municipalities/{municipality}',    [AdminMunicipalityController::class, 'update'])->name('admin.municipalities.update');
    Route::delete('/municipalities/{municipality}', [AdminMunicipalityController::class, 'destroy'])->name('admin.municipalities.destroy');

    // Office management — where constraint prevents 'municipalities' matching the {office} wildcard
    Route::resource('offices', AdminOfficeController::class)->except(['show'])->where(['office' => '[0-9]+'])->names([
        'index'   => 'admin.offices.index',
        'create'  => 'admin.offices.create',
        'store'   => 'admin.offices.store',
        'edit'    => 'admin.offices.edit',
        'update'  => 'admin.offices.update',
        'destroy' => 'admin.offices.destroy',
    ]);

    // Service management for admin
    Route::resource('services', ServiceController::class)->except(['show'])->names([
        'index'   => 'admin.services.index',
        'create'  => 'admin.services.create',
        'store'   => 'admin.services.store',
        'edit'    => 'admin.services.edit',
        'update'  => 'admin.services.update',
        'destroy' => 'admin.services.destroy',
    ]);
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/offices',             [OfficeController::class, 'index'])->name('offices.index');
    Route::get('/offices/create',      [OfficeController::class, 'create'])->name('offices.create');
    Route::post('/offices',            [OfficeController::class, 'store'])->name('offices.store');
    Route::get('/offices/{office}',    [OfficeController::class, 'show'])->name('offices.show');
    Route::delete('/offices/{office}', [OfficeController::class, 'destroy'])->name('offices.destroy');

    // Citizen request management
    Route::get('/requests',                            [CitizenRequestController::class, 'adminIndex'])->name('requests.index');
    Route::patch('/requests/{citizenRequest}/approve', [CitizenRequestController::class, 'approve'])->name('requests.approve');
    Route::patch('/requests/{citizenRequest}/reject',  [CitizenRequestController::class, 'reject'])->name('requests.reject');
    Route::post('/requests/bulk-action',               [CitizenRequestController::class, 'bulkAction'])->name('requests.bulk-action');
});

// Staff
Route::prefix('staff')->name('staff.')->middleware(['auth'])->group(function () {
    Route::resource('services', ServiceController::class)->except(['show']);
});
use App\Http\Controllers\Citizen\ChatbotController;
R