<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Files\FileDeleteServiceInterface;
use App\Contracts\Files\FileUploadServiceInterface;
use App\Contracts\Files\FileViewServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFileRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileApiController extends Controller
{
    public function __construct(
        private readonly FileUploadServiceInterface $fileUploadService,
        private readonly FileDeleteServiceInterface $fileDeleteService,
        private readonly FileViewServiceInterface $fileViewService
    ) {
    }

    // GET /api/files
    public function index(Request $request)
    {
        $files = $request->user()->files->latest()->get();
        return FileResource::collection($files);
    }

    // POST /api/files
    public function store(StoreFileRequest $request): JsonResponse
    {
        $comment = $request->filled('comment') ? $request->string('comment')->toString() : null;

        $expiresAt = $request->date('expires_at')?->toDateTime();

        $file = $this->fileUploadService->upload(
            $request->file('file'),
            Auth::id(),
            $comment,
            $expiresAt
        );

        return response()->json([
            'message' => 'File uploaded successfully',
            'data' => new FileResource($file)
        ], 201);
    }

    // GET /api/files/{file}
    public function show(File $file)
    {
        if ($file->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized operation'], 403);
        }

        $this->fileViewService->incrementViewCount($file);

        return new FileResource($file);
    }

    //DELETE /api/files/{file}
    public function destroy(File $file): JsonResponse
    {
        if ($file->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized operation'], 403);
        }

        $this->fileDeleteService->delete($file);

        return response()->json(['message' => 'File deleted successfully']);
    }
}
