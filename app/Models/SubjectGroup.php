<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubjectGroup extends Model {
    use HasFactory;
    protected $fillable = ['name', 'code'];
    public function placementRecords(): BelongsToMany {
        return $this->belongsToMany(PlacementRecord::class, 'placement_record_subject_group');
    }
}