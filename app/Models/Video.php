<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function getFormattedPublishedAtAttribute(): string
    {
        if (!$this->published_at) {
            return '';
        }
        return Carbon::parse($this->published_at)->isoFormat('D [de] MMMM [de] YYYY');
    }

    public function getFormattedForHumansPublishedAtAttribute(): string
    {
        if (!$this->published_at) {
            return '';
        }
        return Carbon::parse($this->published_at)->diffForHumans();
    }

    public function getPublishedAtTimestampAttribute(): ?int
    {
        if (!$this->published_at) {
            return null;
        }
        return Carbon::parse($this->published_at)->timestamp;
    }
}
