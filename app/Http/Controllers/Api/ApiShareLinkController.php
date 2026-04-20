<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Files\LinkGeneratorServiceInterface;
use App\Contracts\Files\LinkViewerServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\ShareLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiShareLinkController extends Controller
{
    public function __construct(
        private readonly LinkGeneratorServiceInterface $linkGeneratorService,
        private readonly LinkViewerServiceInterface $linkViewerService
    ) {
    }

    // POST /api/files/{file}/links
    public function store(Request $request, File $file): JsonResponse
    {
        if ($file->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized operation'], 403);
        }

        $request->validate([
            'type' => 'required|in:public,one-time'
        ]);

        $link = $this->linkGeneratorService->generate($file, $request->type);

        return response()->json([
            'message' => 'Link generated successfully',
            'link_url' => url('api/share/' . $link->token),
            'type' => $link->type
        ], 201);
    }

    // GET /api/share/{token}
    public function show(string $token): JsonResponse
    {
        $link = ShareLink::where('token', $token)->first();

        if (!$link || !$link->isValid()) {
            return response()->json(['message' => 'Link not found or has expired'], 404);
        }

        $this->linkViewerService->recordView($link);

        return response()->json([
            'message' => 'File retrieved successfully',
            'data' => new FileResource($link->file),
            'image_url' => \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'api.share.image',
                now()->addMinutes(10),
                ['token' => $link->token]
            )
        ]);
    }

    // GET /api/share/{token}/image
    public function image(string $token): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $link = ShareLink::where('token', $token)->firstOrFail();

        /** @var \App\Models\File $file */
        $file = $link->file;

        return \Illuminate\Support\Facades\Storage::response($file->path, $file->name);
    }
}
