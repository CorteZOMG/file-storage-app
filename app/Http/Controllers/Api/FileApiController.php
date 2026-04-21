<?php

namespace App\Http\Controllers\Api;

use OpenApi\Attributes as OAT;
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
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[OAT\Tag(name: "Files", description: "File management endpoints")]
class FileApiController extends Controller
{
    public function __construct(
        private readonly FileUploadServiceInterface $fileUploadService,
        private readonly FileDeleteServiceInterface $fileDeleteService,
        private readonly FileViewServiceInterface $fileViewService
    ) {
    }

    // GET /api/files
    #[OAT\Get(path: "/files", summary: "List all user files", tags: ["Files"], security: [["bearerAuth" => []]])]
    #[OAT\Response(response: 200, description: "List of files")]
    public function index(Request $request)
    {
        $files = $request->user()->files()->latest()->get();
        return FileResource::collection($files);
    }

    // POST /api/files
    #[OAT\Post(path: "/files", summary: "Upload a new file", tags: ["Files"], security: [["bearerAuth" => []]])]
    #[OAT\RequestBody(required: true, content: new OAT\MediaType(
        mediaType: "multipart/form-data",
        schema: new OAT\Schema(
            required: ["file"],
            properties: [
                new OAT\Property(property: "file", type: "string", format: "binary"),
                new OAT\Property(property: "comment", type: "string"),
                new OAT\Property(property: "expires_at", type: "string", format: "date-time")
            ]
        )
    ))]
    #[OAT\Response(response: 201, description: "File uploaded")]
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
    #[OAT\Get(path: "/files/{file}", summary: "Get details of a file", tags: ["Files"], security: [["bearerAuth" => []]])]
    #[OAT\Parameter(name: "file", in: "path", required: true, schema: new OAT\Schema(type: "integer"))]
    #[OAT\Response(response: 200, description: "File details")]
    public function show(File $file)
    {
        if ($file->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized operation'], 403);
        }

        $this->fileViewService->incrementViewCount($file);
        $file->load('shareLinks');

        return new FileResource($file);
    }

    // GET /api/files/{file}/download
    #[OAT\Get(path: "/files/{file}/download", summary: "Download a file", tags: ["Files"], security: [["bearerAuth" => []]])]
    #[OAT\Parameter(name: "file", in: "path", required: true, schema: new OAT\Schema(type: "integer"))]
    #[OAT\Response(response: 200, description: "File content")]
    public function download(File $file): StreamedResponse|JsonResponse
    {
        if ($file->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized operation'], 403);
        }

        return Storage::response($file->path, $file->name);
    }

    // GET /api/files/{file}/preview
    #[OAT\Get(path: "/files/{file}/preview", summary: "Preview a file", tags: ["Files"], security: [["bearerAuth" => []]])]
    #[OAT\Parameter(name: "file", in: "path", required: true, schema: new OAT\Schema(type: "integer"))]
    #[OAT\Response(response: 200, description: "File content")]
    public function preview(File $file): StreamedResponse|JsonResponse
    {
        if ($file->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized operation'], 403);
        }

        return Storage::response($file->path);
    }

    // DELETE /api/files/{file}
    #[OAT\Delete(path: "/files/{file}", summary: "Delete a file", tags: ["Files"], security: [["bearerAuth" => []]])]
    #[OAT\Parameter(name: "file", in: "path", required: true, schema: new OAT\Schema(type: "integer"))]
    #[OAT\Response(response: 200, description: "File deleted")]
    public function destroy(File $file): JsonResponse
    {
        if ($file->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized operation'], 403);
        }

        $this->fileDeleteService->delete($file);

        return response()->json(['message' => 'File deleted successfully']);
    }
}
