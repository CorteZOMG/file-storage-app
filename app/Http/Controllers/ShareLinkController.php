<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\ShareLink;
use App\Contracts\Files\LinkGeneratorServiceInterface;
use App\Contracts\Files\LinkViewerServiceInterface;
use App\Contracts\Files\FileViewServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ShareLinkController extends Controller
{
    public function __construct(
        private readonly LinkGeneratorServiceInterface $linkGeneratorService,
        private readonly LinkViewerServiceInterface $linkViewerService,
        private readonly FileViewServiceInterface $fileViewService
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

        /** @var \App\Models\File $file */
        $file = $link->file;
        $this->fileViewService->incrementViewCount($file);

        $imageUrl = URL::temporarySignedRoute('shared.image', now()->addMinutes(5), ['token' => $link->token]);

        /** @var view-string $viewName */
        $viewName = 'shared.show';
        return view($viewName, ['link' => $link, 'imageUrl' => $imageUrl]);
    }

    public function image(string $token): StreamedResponse
    {
        $link = ShareLink::where('token', $token)->firstOrFail();

        /** @var \App\Models\File $file */
        $file = $link->file;

        return Storage::response($file->path, $file->name);
    }
}
