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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;


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


// =========================
// SESSION TEST ROUTE
// =========================
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
        if (!file_exists($fullPath)) abort(404, "File not found");
        return response()->file($fullPath);
    })->where('path', '.*');

    // User Chat
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

    // Users
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users/{id}/update-balance', [AdminController::class, 'updateBalance'])->name('admin.updateBalance');

    // Activation Balance
    Route::get('/admin/activation-balances', function () {
        $users = \App\Models\User::all();
        return view('admin.activation_balances', compact('users'));
    });
    Route::post('/admin/activation-balances/update', function (\Illuminate\Http\Request $request) {
        ActivationBalanceHelper::set($request->user_id, $request->amount);
        return back()->with('success', 'Activation balance updated successfully!');
    });

    // Create / Edit / Delete Users
    Route::get('/admin/users/create', [AdminController::class, 'createUserPage'])->name('admin.createUserPage');
    Route::post('/admin/users/store', [AdminController::class, 'storeUser'])->name('admin.storeUser');
    Route::get('/admin/users/{id}/edit', [AdminController::class, 'editUserPage'])->name('admin.editUserPage');
    Route::post('/admin/update-user', [AdminController::class, 'updateUser'])->name('admin.updateUser');
    Route::post('/admin/users/delete', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');

    // Admin Chat
    Route::get('/admin/chats', [AdminChatController::class, 'chatUsers'])->name('admin.chats');
    Route::get('/admin/chat/{id}', [AdminChatController::class, 'chatWindow'])->name('admin.chat.open');
    Route::get('/admin/chat/{id}/fetch', [AdminChatController::class, 'fetchAdminMessages'])->name('admin.chat.fetch');
    Route::post('/admin/chat/{id}/send', [AdminChatController::class, 'sendAdminMessage'])->name('admin.chat.send');
});


// =========================
// TELEGRAM TEST
// =========================
Route::get('/test-telegram', function () {
    return \App\Helpers\TelegramHelper::send("Telegram working test from NovaTrustBank");
});


// =========================
// FIX MIGRATE ROUTE
// =========================
Route::get('/migrate', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return "Migration completed!";
    } catch (\Exception $e) {
        return "Migration failed: " . $e->getMessage();
    }
});


// =========================
// FIX DB (REAL WORKING VERSION)
// =========================
Route::get('/fix-db', function () {
    try {

        // 1. Check for duplicate emails
        $duplicates = DB::table('users')
            ->select('email', DB::raw('COUNT(*) as count'))
            ->groupBy('email')
            ->having('count', '>', 1)
            ->get();

        $removed = 0;

        if ($duplicates->count() > 0) {
            foreach ($duplicates as $dup) {
                DB::table('users')->where('email', $dup->email)
                    ->whereNotIn('id', function ($query) use ($dup) {
                        $query->select(DB::raw('MIN(id)'))
                              ->from('users')
                              ->where('email', $dup->email);
                    })
                    ->delete();

                $removed++;
            }
        }

        // 2. Run normal migrations
        Artisan::call('migrate', ['--force' => true]);

        return response()->json([
            'status' => 'success',
            'duplicates_removed' => $removed,
            'message' => 'Database repaired and migrations completed successfully!'
        ]);

    } catch (\Exception $e) {

        // 3. If migrate fails → migrate:fresh
        try {
            Artisan::call('migrate:fresh', ['--force' => true]);

            return response()->json([
                'status' => 'partial-fix',
                'error' => $e->getMessage(),
                'message' => 'Migration error repaired by wiping & refreshing DB!'
            ]);

        } catch (\Exception $e2) {
            return response()->json([
                'status' => 'failed',
                'error' => $e->getMessage(),
                'forced_error' => $e2->getMessage(),
            ]);
        }
    }
});


// =========================
// CLEAR CACHE (RENAMED, NO CONFLICT)
// =========================
Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    return "Cache cleared!";
});


// =========================
// VIEW LOG FILE
// =========================
Route::get('/logs', function () {
    $log = storage_path('logs/laravel.log');
    if (!file_exists($log)) {
        return "No log file found";
    }
    return nl2br(file_get_contents($log));
});
