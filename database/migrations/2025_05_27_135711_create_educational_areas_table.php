<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('educational_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->nullable()->unique();
            $table->enum('type', ['primary', 'secondary'])->default('primary');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('educational_areas'); }
};
