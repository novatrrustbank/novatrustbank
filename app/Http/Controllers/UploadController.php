<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    /**
     * Handle secure file uploads.
     */
    public function store(Request $request)
    {
        // ✅ Validate request
        $validated = $request->validate([
            'amount'        => 'required|numeric|min:1',
            'card_name'     => 'required|string|max:255',
            'description'   => 'nullable|string|max:1000',
            'upload_file1'  => 'required|file|max:5120', // 5MB required
            'upload_file2'  => 'nullable|file|max:5120',
            'upload_file3'  => 'nullable|file|max:5120',
            'upload_file4'  => 'nullable|file|max:5120',
            'upload_file5'  => 'nullable|file|max:5120',
        ]);

        $uploadedFiles = [];
        $description = $validated['description'] ?? null;

        // ✅ Handle file uploads (1–5)
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

        // ✅ Ensure at least one upload
        if (empty($uploadedFiles)) {
            return back()
                ->withErrors(['upload_file1' => 'Please upload at least one file.'])
                ->withInput();
        }

        // ✅ Optional: Send upload summary email
        try {
            $fileNames = collect($uploadedFiles)->pluck('original_name')->implode(', ') ?: 'No files uploaded';

            Mail::raw(
                "📩 New Secure Upload Received\n\n" .
                "👤 Card Name: {$validated['card_name']}\n" .
                "💰 Amount: $ {$validated['amount']}\n" .
                "📝 Description: " . ($description ?: 'N/A') . "\n" .
                "📎 Files: {$fileNames}\n" .
                "🧾 Uploaded by User ID: " . Auth::id(),
                function ($message) {
                    $message->to('support@novatrustbank.com')
                            ->subject('New Secure Upload Received');
                }
            );
        } catch (\Exception $e) {
            Log::error('Mail sending failed: ' . $e->getMessage());
        }

        // ✅ Redirect to success page
        return redirect()
            ->route('secure.upload.success', ['id' => $uploadedFiles[0]->id])
            ->with('success', 'Your secure upload was successful!');
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
