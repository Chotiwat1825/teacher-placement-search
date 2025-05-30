<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlacementRecord extends Model
{
    use HasFactory;

    // สถานะที่เป็นไปได้ (เพื่อให้จัดการง่ายขึ้น)
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    // public const STATUS_DRAFT = 'draft'; // ถ้ามี

    protected $fillable = [
        'academic_year',
        'announcement_date',
        'educational_area_id',
        'round_number',
        'source_link',
        'user_id', // ID ของผู้สร้าง/ผู้ส่งข้อมูล
        'placement_type_id', // เพิ่ม
        'notes', // เพิ่ม
        'status', // เพิ่ม
        'rejection_reason', // เพิ่ม (ถ้ามี)
        'processed_by_user_id', // เพิ่ม (ถ้ามี)
        'processed_at', // เพิ่ม (ถ้ามี)
    ];

    protected $casts = [
        'announcement_date' => 'datetime:Y-m-d',
        'processed_at' => 'datetime', // เพิ่ม (ถ้ามี)
        // is_active ของ placement record เอง (ถ้ามี)
        // 'is_active' => 'boolean',
    ];

    // Relationship ไปยัง User (ผู้สร้าง/ผู้ส่งข้อมูล)
    // ถ้า user_id หมายถึงผู้สร้างเสมอ ไม่ว่าจะเป็น Admin หรือ User ทั่วไป
    public function creator(): BelongsTo
    {
        // หรือ submittedBy() หรือ author()
        // หรือ submittedBy() หรือ author()
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship ไปยัง User (Admin ที่อนุมัติ/ปฏิเสธ) (ถ้ามี)
    public function processor(): BelongsTo
    {
        // หรือ processedBy()
        // หรือ processedBy()
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }

    public function educationalArea(): BelongsTo
    {
        return $this->belongsTo(EducationalArea::class);
    }

    public function subjectGroups(): BelongsToMany
    {
        return $this->belongsToMany(SubjectGroup::class, 'placement_record_subject_group');
    }

    public function placementType(): BelongsTo
    {
        // <<<< เพิ่ม Relationship นี้
        // <<<< เพิ่ม Relationship นี้
        return $this->belongsTo(PlacementType::class, 'placement_type_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PlacementAttachment::class);
    }

    // Helper Scopes (Optional)
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }
}
