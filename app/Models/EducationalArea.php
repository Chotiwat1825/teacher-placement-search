<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducationalArea extends Model {
    use HasFactory;
    protected $fillable = ['name', 'code', 'type'];
    public function placementRecords(): HasMany { return $this->hasMany(PlacementRecord::class); }
}
