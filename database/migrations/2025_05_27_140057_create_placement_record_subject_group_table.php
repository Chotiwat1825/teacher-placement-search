<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('placement_record_subject_group', function (Blueprint $table) {
            $table->foreignId('placement_record_id')->constrained('placement_records')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('subject_group_id')->constrained('subject_groups')->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['placement_record_id', 'subject_group_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('placement_record_subject_group'); }
};
