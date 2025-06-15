<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
        $table->boolean('is_admin')->default(false)->after('email');
        $table->string('phone_number')->nullable()->after('is_admin');
        $table->text('bio')->nullable()->after('phone_number');
        $table->string('profile_picture')->nullable()->after('bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['is_admin', 'phone_number', 'bio', 'profile_picture']);
        });
    }
};
