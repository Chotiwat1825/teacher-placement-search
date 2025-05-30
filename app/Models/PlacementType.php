<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Import HasMany

class PlacementType extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'placement_types'; // ชื่อตาราง (ถ้าไม่ตรงกับ convention ของ Laravel)

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'description', 'is_active'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean', // Cast is_active เป็น boolean
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all of the placement records for the PlacementType.
     * (หนึ่ง PlacementType สามารถมีได้หลาย PlacementRecord)
     */
    public function placementRecords(): HasMany
    {
        return $this->hasMany(PlacementRecord::class, 'placement_type_id');
        // 'placement_type_id' คือ foreign key ในตาราง 'placement_records'
    }
}
