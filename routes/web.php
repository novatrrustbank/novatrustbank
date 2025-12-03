<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminChatController;
use App\Helpers\ActivationBalanceHelper;
use Illuminate\Support\Facades\Storage;


// =========================
// PUBLIC ROUTES
// =========================
Route::get('/', function () {
    return view('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// =======================================
// SESSION TEST ROUTE (MUST BE PUBLIC)
// =======================================
Route::get('/session-test', function () {
    session(['test_key' => 'working']);

    return response()->json([
        'session_set'   => true,
        'session_value' => session('test_key'),
        'app_url'       => config('app.url'),
        'session_domain'=> config('session.domain'),
        'same_site'     => config('session.same_site'),
        'secure_cookie' => config('session.secure'),
        'driver'        => config('session.driver'),
    ]);
});


// =========================
// USER ROUTES (AUTH REQUIRED)
// =========================
Route::middleware(['auth'])->group(function () {

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

    // Serve Chat Files
    Route::get('/chat-file/{path}', function ($path) {
        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists($fullPath)) {
            abort(404, "File not found");
        }

        return response()->file($fullPath);
    })->where('path', '.*');

    // ============================
    // USER LIVE CHAT
    // ============================
    Route::get('/chat', [ChatController::class, 'userChat'])->name('user.chat');
    Route::get('/chat/fetch', [ChatController::class, 'fetchMessages'])->name('chat.fetch');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/unread/count', [ChatController::class, 'unreadCount'])->name('messages.unread.count');
});


// =========================
// ADMIN ROUTES
// =========================
Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Manage Users
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users/{id}/update-balance', [AdminController::class, 'updateBalance'])->name('admin.updateBalance');

    // Activation Balances
    Route::get('/admin/activation-balances', function () {
        $users = \App\Models\User::all();
        return view('admin.activation_balances', compact('users'));
    });
    Route::post('/admin/activation-balances/update', function (\Illuminate\Http\Request $request) {
        ActivationBalanceHelper::set($request->user_id, $request->amount);
        return back()->with('success', 'Activation balance updated successfully!');
    });

    // Edit User Name
    Route::get('/admin/user/{id}/edit-name', [AdminController::class, 'editUserNamePage'])->name('admin.editUserNamePage');
    Route::post('/admin/user/update-name', [AdminController::class, 'updateUserName'])->name('admin.updateUserName');

    // Create User
    Route::get('/admin/users/create', [AdminController::class, 'createUserPage'])->name('admin.createUserPage');
    Route::post('/admin/users/store', [AdminController::class, 'storeUser'])->name('admin.storeUser');

    // Edit User
    Route::get('/admin/users/{id}/edit', [AdminController::class, 'editUserPage'])->name('admin.editUserPage');
    Route::post('/admin/update-user', [AdminController::class, 'updateUser'])->name('admin.updateUser');

    // Delete User
    Route::post('/admin/users/delete', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');

    // =============================
    // ADMIN LIVE CHAT
    // =============================
    Route::get('/admin/chats', [AdminChatController::class, 'chatUsers'])->name('admin.chats');
    Route::get('/admin/chat/{id}', [AdminChatController::class, 'chatWindow'])->name('admin.chat.open');
    Route::get('/admin/chat/{id}/fetch', [AdminChatController::class, 'fetchAdminMessages'])->name('admin.chat.fetch');
    Route::post('/admin/chat/{id}/send', [AdminChatController::class, 'sendAdminMessage'])->name('admin.chat.send');
});
