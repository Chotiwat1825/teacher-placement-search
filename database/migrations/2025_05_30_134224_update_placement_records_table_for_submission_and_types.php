<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('placement_records', function (Blueprint $table) {
            // 1. คอลัมน์สำหรับ "ประเภทการบรรจุ" (จากหัวข้อก่อนหน้า)
            // ตรวจสอบว่าคอลัมน์นี้ยังไม่มีอยู่จริงก่อนเพิ่ม
            if (!Schema::hasColumn('placement_records', 'placement_type_id')) {
                $table
                    ->foreignId('placement_type_id')
                    ->nullable() // อนุญาตให้เป็น null หรือกำหนด default ถ้าต้องการ
                    ->after('round_number') // หรือตำแหน่งที่เหมาะสม
                    ->constrained('placement_types') // อ้างอิงตาราง placement_types
                    ->onUpdate('cascade')
                    ->onDelete('set null'); // หรือ onDelete('restrict') ถ้าประเภทต้องมีเสมอ
            }

            // 2. คอลัมน์สำหรับ "หมายเหตุ" (จากหัวข้อก่อนหน้า)
            if (!Schema::hasColumn('placement_records', 'notes')) {
                $table->text('notes')->nullable()->after('source_link'); // หรือตำแหน่งที่เหมาะสม
            }

            // 3. คอลัมน์สำหรับ "สถานะการอนุมัติ"
            // enum: 'pending', 'approved', 'rejected'
            // อาจจะเพิ่ม 'draft' ถ้า user สามารถบันทึกแบบร่างได้
            if (!Schema::hasColumn('placement_records', 'status')) {
                $table->string('status')->default('pending')->index()->after('user_id');
                // default เป็น 'pending' สำหรับข้อมูลใหม่ที่ user ส่งเข้ามา
                // index() เพื่อประสิทธิภาพในการ query ตาม status
            }

            // 4. ปรับปรุงคอลัมน์ user_id (ถ้าจำเป็น)
            // เดิม user_id อาจจะหมายถึง admin ที่สร้าง
            // ตอนนี้จะให้หมายถึง "ผู้สร้างข้อมูล" ซึ่งอาจจะเป็น User ทั่วไป หรือ Admin ก็ได้
            // ตรวจสอบว่า user_id มีอยู่แล้วหรือไม่ และ type ถูกต้องหรือไม่ (ควรจะเป็น foreignId nullable)
            // ถ้า user_id เดิมเป็น non-nullable และหมายถึง admin เท่านั้น อาจจะต้องพิจารณา rename หรือเพิ่มคอลัมน์ใหม่
            // ในที่นี้จะสมมติว่า user_id ที่มีอยู่แล้ว จะใช้สำหรับเก็บ ID ของผู้สร้าง (ทั้ง User และ Admin)
            // และ nullable ถ้าข้อมูลถูก import มาโดยไม่มีผู้สร้างที่ระบุ
            // ถ้า user_id มีอยู่แล้วและต้องการเปลี่ยนให้ nullable:
            // if (Schema::hasColumn('placement_records', 'user_id')) {
            //     $table->foreignId('user_id')->nullable()->change();
            // }

            // 5. (Optional) คอลัมน์สำหรับเหตุผลในการปฏิเสธ
            if (!Schema::hasColumn('placement_records', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('status');
            }

            // 6. (Optional) คอลัมน์สำหรับ Admin ที่อนุมัติ/ปฏิเสธ
            if (!Schema::hasColumn('placement_records', 'processed_by_user_id')) {
                $table
                    ->foreignId('processed_by_user_id') // Admin ID
                    ->nullable()
                    ->after('rejection_reason')
                    ->constrained('users') // อ้างอิงตาราง users
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            }

            // 7. (Optional) คอลัมน์สำหรับวันที่ทำการอนุมัติ/ปฏิเสธ
            if (!Schema::hasColumn('placement_records', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('processed_by_user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('placement_records', function (Blueprint $table) {
            // ลบคอลัมน์ตามลำดับย้อนกลับของการเพิ่ม
            // และต้อง drop foreign key constraints ก่อน drop คอลัมน์
            if (Schema::hasColumn('placement_records', 'processed_at')) {
                $table->dropColumn('processed_at');
            }

            if (Schema::hasColumn('placement_records', 'processed_by_user_id')) {
                // ชื่อ constraint อาจจะเป็น 'placement_records_processed_by_user_id_foreign'
                // ตรวจสอบชื่อ constraint ให้ถูกต้องถ้าเกิด error
                try {
                    $table->dropForeign(['processed_by_user_id']);
                } catch (\Exception $e) {
                }
                $table->dropColumn('processed_by_user_id');
            }

            if (Schema::hasColumn('placement_records', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }

            if (Schema::hasColumn('placement_records', 'status')) {
                $table->dropIndex(['status']); // Drop index ก่อน
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('placement_records', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('placement_records', 'placement_type_id')) {
                try {
                    $table->dropForeign(['placement_type_id']);
                } catch (\Exception $e) {
                }
                $table->dropColumn('placement_type_id');
            }
            // การ rollback user_id อาจจะซับซ้อน ขึ้นอยู่กับว่าคุณเปลี่ยนอะไรไปบ้าง
            // ถ้าแค่เปลี่ยนเป็น nullable ก็เปลี่ยนกลับเป็น non-nullable (ถ้าเหมาะสม)
        });
    }
};
