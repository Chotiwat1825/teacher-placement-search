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
        Schema::create('placement_types', function (Blueprint $table) {
            $table->id(); // Primary Key, Auto-increment (bigIncrements)
            $table->string('name')->unique(); // ชื่อประเภทการบรรจุ, ไม่ซ้ำกัน
            $table->text('description')->nullable(); // คำอธิบายเพิ่มเติม (optional)
            $table->boolean('is_active')->default(true); // สถานะการใช้งาน (เผื่อต้องการปิด/เปิดบางประเภท)
            $table->timestamps(); // created_at และ updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('placement_types');
    }
};
