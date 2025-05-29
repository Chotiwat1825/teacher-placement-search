<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class PlacementRecord extends Model {
    use HasFactory;
    protected $fillable = ['academic_year', 'announcement_date', 'educational_area_id', 'round_number', 'source_link', 'user_id'];
    protected $casts = ['announcement_date' => 'datetime:Y-m-d'];

    public function educationalArea(): BelongsTo { return $this->belongsTo(EducationalArea::class); }
    public function subjectGroups(): BelongsToMany {
        return $this->belongsToMany(SubjectGroup::class, 'placement_record_subject_group');
    }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function attachments(): HasMany { return $this->hasMany(PlacementAttachment::class); }
}