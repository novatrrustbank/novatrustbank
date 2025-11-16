<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MessageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect homepage → login
Route::get('/', fn() => redirect()->route('login'));


// ====================== AUTH ROUTES ======================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ====================== USER ROUTES ======================
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transfers
    Route::get('/transfer', [TransferController::class, 'showForm'])->name('transfer.form');
    Route::post('/transfer', [TransferController::class, 'processTransfer'])->name('transfer.process');
    Route::get('/transfer-success', [TransferController::class, 'success'])->name('transfer.success');

    // Transaction History
    Route::get('/history', [TransactionController::class, 'index'])->name('history');

    // Secure Upload
    Route::get('/secure_upload', fn() => view('secure_upload'))->name('secure.upload');
    Route::post('/secure_upload', [UploadController::class, 'store'])->name('secure.upload.post');
    Route::get('/upload_success/{id}', [UploadController::class, 'success'])->name('secure.upload.success');


    // ====================== USER CHAT ROUTES ======================

    Route::get('/chat', [MessageController::class, 'userChat'])->name('user.chat');
    Route::post('/chat/send', [MessageController::class, 'store'])->name('chat.send');
    Route::get('/chat/messages/{userId}', [MessageController::class, 'fetchMessages'])->name('chat.messages');

    Route::post('/chat/typing', [MessageController::class, 'typing'])->name('chat.typing');
    Route::get('/chat/typing/{userId}', [MessageController::class, 'typingStatus'])->name('chat.typing.status');

    Route::post('/chat/mark-read', [MessageController::class, 'markRead'])->name('chat.mark.read');
    Route::get('/chat/online/{userId}', [MessageController::class, 'onlineStatus'])->name('chat.online.status');

    Route::get('/chat-with-admin', [MessageController::class, 'userChat'])->name('chat.with.admin');
});


// ====================== ADMIN ROUTES ======================
Route::middleware(['auth', 'admin'])->group(function () {

    // Admin Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // Admin sends message
    Route::post('/admin/chat/send', [MessageController::class, 'store'])->name('admin.chat.send');

    // Open chat with a specific user
    Route::get('/admin/chat/{id}', [MessageController::class, 'adminChat'])->name('admin.chat.window');

    // Auto-refresh messages
    Route::get('/admin/chat/{user_id}/refresh', [MessageController::class, 'fetchMessages'])->name('admin.chat.refresh');

    // List all chats
    Route::get('/admin/chats', [MessageController::class, 'adminIndex'])->name('admin.chats');
});


// ====================== TEST MAIL ROUTE ======================
Route::get('/test-mail', function () {
    try {
        Mail::raw('✅ SendGrid test from NovaTrust Bank.', function ($message) {
            $message->to('collaomn@gmail.com')->subject('SendGrid Test Email');
        });
        return '✅ Test email sent successfully!';
    } catch (\Exception $e) {
        return '❌ Error: ' . $e->getMessage();
    }
});
