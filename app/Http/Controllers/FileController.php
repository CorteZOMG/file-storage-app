<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreFileRequest;
use App\Services\Files\FileUploadService;
use App\Services\Files\FileViewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\File;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    public function __construct(
        private readonly FileUploadService $fileUploadService,
        private readonly FileViewService $fileViewService
    ) {
    }

    public function index(Request $request): View
    {
        $files = $request->user()->files()->latest()->get();

        return view('files.index', ['files' => $files]);
    }

    public function show(File $file): View
    {
        $this->fileViewService->incrementViewCount($file);
        return view('files.show', ['file' => $file]);
    }

    public function download(File $file): StreamedResponse
    {
        return Storage::response($file->path, $file->name);
    }

    public function store(StoreFileRequest $request): RedirectResponse
    {
        $comment = $request->filled('comment') ? $request->string('comment')->toString() : null;

        $expiresAt = $request->date('expires_at')?->toDateTime();

        $this->fileUploadService->upload(
            $request->file('file'),
            Auth::id(),
            $comment,
            $expiresAt
        );

        return redirect()->back()->with('status', 'File uploaded successfully!');
    }
}
