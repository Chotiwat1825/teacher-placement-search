<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_image')->nullable()->after('password');
            $table->boolean('is_admin')->default(false)->after('remember_token');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_image', 'is_admin']);
        });
    }
};
