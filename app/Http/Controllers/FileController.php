<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileRequest;
use App\Services\Files\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FileController extends Controller
{
    public function __construct(
        private readonly FileUploadService $fileUploadService
    ) {
    }

    public function index(): View
    {
        return view('files.index');
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
