<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['file_id', 'token', 'type', 'views'])]
class ShareLink extends Model
{
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function isOneTime(): bool
    {
        return $this->type === 'one-time';
    }

    public function isPublic(): bool
    {
        return $this->type === 'public';
    }

    public function isValid(): bool
    {
        if (! $this->file) {
            return false;
        }

        if ($this->isOneTime() && $this->views > 0) {
            return false;
        }

        return true;
    }
}
