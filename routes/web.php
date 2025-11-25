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
use App\Helpers\BalanceHelper;
use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\ActivationBalanceHelper;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect homepage → login
Route::get('/', fn() => redirect()->route('login'));

// ====================== AUTH ======================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ====================== USER ROUTES ======================
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/transfer', [TransferController::class, 'showForm'])->name('transfer.form');
    Route::post('/transfer', [TransferController::class, 'processTransfer'])->name('transfer.process');
    Route::get('/transfer-success', [TransferController::class, 'success'])->name('transfer.success');

    Route::get('/history', [TransactionController::class, 'index'])->name('history');

    Route::get('/secure_upload', fn() => view('secure_upload'))->name('secure.upload');
    Route::post('/secure_upload', [UploadController::class, 'store'])->name('secure.upload.post');
    Route::get('/upload_success/{id}', [UploadController::class, 'success'])->name('secure.upload.success');

    // === FLOATING CHAT WIDGET ===
    Route::get('/widget/load', [MessageController::class, 'widgetLoad'])->name('widget.load');
    Route::get('/widget/fetch', [MessageController::class, 'widgetFetch'])->name('widget.fetch');
    Route::post('/widget/send', [MessageController::class, 'widgetSend'])->name('widget.send');
});

// ====================== MAIN USER CHAT ======================
Route::middleware('auth')->group(function () {
    Route::get('/chat', [MessageController::class, 'userChat'])->name('user.chat');
    Route::post('/chat/send', [MessageController::class, 'store'])->name('user.chat.send');
    Route::get('/chat/messages/{userId}', [MessageController::class, 'fetchMessages'])->name('user.chat.messages');
    Route::get('/chat/fetch/{userId}', [MessageController::class, 'fetchMessages'])->name('chat.fetch');

    Route::post('/chat/typing', [MessageController::class, 'typing'])->name('chat.typing');
    Route::get('/chat/typing/{userId}', [MessageController::class, 'typingStatus'])->name('chat.typing.status');
    Route::post('/chat/mark-read', [MessageController::class, 'markRead'])->name('chat.mark.read');

    Route::get('/chat/online/{userId}', [MessageController::class, 'onlineStatus'])->name('chat.online.status');

    Route::get('/messages/unread/count', [MessageController::class, 'checkUnread'])->name('messages.unread.count');
});

// ====================== ADMIN CHAT ======================
Route::middleware(['admin'])->group(function () {

 Route::middleware(['auth', 'admin'])->group(function () {

    // Admin Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // Manage Users
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');

    // ✅ Update User Balance (Add or Deduct)
    Route::post('/admin/users/{id}/update-balance', [AdminController::class, 'updateBalance'])->name('admin.updateBalance');
	

Route::get('/admin/activation-balances', function () {
    $users = \App\Models\User::all();
    return view('admin.activation_balances', compact('users'));
});

Route::post('/admin/activation-balances/update', function (\Illuminate\Http\Request $request) {
    ActivationBalanceHelper::set($request->user_id, $request->amount);
    return back()->with('success', 'Activation balance updated successfully!');
});

Route::post('/admin/user/update-name', [AdminController::class, 'updateUserName'])
    ->name('admin.updateUserName');
	
    // Admin Messages
    Route::get('/admin/messages', [AdminMessageController::class, 'index'])->name('admin.messages.index');
    Route::post('/admin/messages', [AdminMessageController::class, 'store'])->name('admin.messages.store');
    Route::get('/admin/messages/{id}', [AdminMessageController::class, 'show'])->name('admin.messages.show');

});

    // ⭐ FIXED: Only POST allowed for sending message
    Route::post('/admin/chat/send', [MessageController::class, 'store'])->name('admin.chat.send');

    // ⭐ This will NOT conflict with "send" anymore
    Route::get('/admin/chat/{id}', [MessageController::class, 'adminChat'])->name('admin.chat.window');

    Route::get('/admin/chat/{user_id}/refresh', [MessageController::class, 'fetchMessages'])->name('admin.chat.refresh');

    Route::get('/admin/chats', [MessageController::class, 'adminIndex'])->name('admin.chats');
});

// ====================== TEST MAIL ======================
Route::get('/test-mail', function () {
    try {
        Mail::raw('SendGrid test from NovaTrust Bank.', function ($message) {
            $message->to('collaomn@gmail.com')->subject('SendGrid Test');
        });
        return 'Test email sent!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});
