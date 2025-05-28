<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('placement_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('placement_record_id')->constrained('placement_records')->onUpdate('cascade')->onDelete('cascade');
            $table->string('file_path');
            $table->string('original_filename');
            $table->string('mime_type')->nullable();
            $table->enum('type', ['file', 'image'])->default('file');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('placement_attachments'); }
};