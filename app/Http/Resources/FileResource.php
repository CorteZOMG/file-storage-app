<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\File
 */
class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'comment' => $this->comment,
            'views' => $this->view_count,

            'download_url' => url("/api/files/{$this->id}/download"),
            'preview_url' => url("/api/files/{$this->id}/preview"),
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at,
        ];

        if ($this->relationLoaded('shareLinks')) {
            $data['share_links'] = $this->shareLinks->map(fn ($link) => [
                'id' => $link->id,
                'token' => $link->token,
                'type' => $link->type,
                'views' => $link->views,
                'is_valid' => $link->isValid(),
                'url' => url("api/share/{$link->token}"),
                'created_at' => $link->created_at,
            ]);
        }

        return $data;
    }
}
