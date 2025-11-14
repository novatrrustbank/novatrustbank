Web.php - Fully working 


<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| These routes handle both user and admin access.
|--------------------------------------------------------------------------
*/

// === Homepage redirect ===
Route::get('/', fn() => redirect()->route('login'));

// === Authentication ===
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// === Authenticated User Routes ===
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transfers
    Route::get('/transfer', [TransferController::class, 'showForm'])->name('transfer.form');
    Route::post('/transfer', [TransferController::class, 'processTransfer'])->name('transfer.process');
    Route::get('/transfer-success', [TransferController::class, 'success'])->name('transfer.success');

    // Transaction History
    Route::get('/history', [TransactionController::class, 'index'])->name('history');

    // ✅ Secure Upload Routes
    Route::get('/secure_upload', function () {
        return view('secure_upload');
    })->name('secure.upload'); // <-- changed this line

    Route::post('/secure_upload', [UploadController::class, 'store'])
        ->name('secure.upload.post');

    Route::get('/upload_success/{id}', [UploadController::class, 'success'])
        ->name('secure.upload.success');
});

// === Admin Routes (auth + admin middleware) ===
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
});


Route::get('/test-mail', function () {
    try {
        Mail::raw('✅ SendGrid test from NovaTrust Bank.', function ($message) {
            $message->to('collaomn@gmail.com')
                    ->subject('SendGrid Test Email');
        });
        return '✅ Test email sent successfully!';
    } catch (\Exception $e) {
        return '❌ Error: ' . $e->getMessage();
    }
});