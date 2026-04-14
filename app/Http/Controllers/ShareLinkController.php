<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\ShareLink;
use App\Services\Files\LinkGeneratorService;
use App\Services\Files\LinkViewerService;
use App\Services\Files\FileViewService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ShareLinkController extends Controller
{
    public function __construct(
        private readonly LinkGeneratorService $linkGeneratorService,
        private readonly LinkViewerService $linkViewerService,
        private readonly FileViewService $fileViewService
    ) {
    }

    public function store(Request $request, File $file): RedirectResponse
    {
        if ($file->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'type' => 'required|in:public,one-time',
        ]);

        $this->linkGeneratorService->generate($file, $validated['type']);

        return redirect()->back()->with('status', 'Link generated successfully!');
    }

    public function show(string $token): View
    {
        $link = ShareLink::where('token', $token)->firstOrFail();

        if ($link->hasBeenUsed()) {
            abort(404, 'This one-time link has already been used and is no longer active.');
        }

        $this->linkViewerService->recordView($link);
        $this->fileViewService->incrementViewCount($link->file);

        $imageUrl = URL::temporarySignedRoute('shared.image', now()->addMinutes(5), ['token' => $link->token]);

        return view('shared.show', ['link' => $link, 'imageUrl' => $imageUrl]);
    }

    public function image(string $token): StreamedResponse
    {
        $link = ShareLink::where('token', $token)->firstOrFail();
        return Storage::response($link->file->path, $link->file->name);
    }
}
