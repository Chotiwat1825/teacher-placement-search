<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlacementAttachment extends Model {
    use HasFactory;
    protected $fillable = ['placement_record_id', 'file_path', 'original_filename', 'mime_type', 'type'];
    public function placementRecord(): BelongsTo { return $this->belongsTo(PlacementRecord::class); }
}