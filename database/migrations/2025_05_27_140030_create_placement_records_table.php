<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('placement_records', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('academic_year')->unsigned(); // เปลี่ยนเป็น smallInteger
            $table->date('announcement_date');
            $table->foreignId('educational_area_id')->constrained('educational_areas')->onUpdate('cascade')->onDelete('restrict');
            // subject_group_id ถูกลบออก เพราะจะใช้ pivot table
            $table->integer('round_number');
            $table->text('source_link')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
            $table->timestamps();
            $table->index(['academic_year', 'educational_area_id', 'round_number'], 'placement_search_index');
        });
    }
    public function down(): void { Schema::dropIfExists('placement_records'); }
};
