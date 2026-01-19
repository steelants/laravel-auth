<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('totp_secret')->nullable()->after('remember_token');
            $table->timestamp('totp_confirmed_at')->nullable()->after('totp_secret');
			 $table->boolean('totp_force')->default(false)->after('totp_confirmed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['totp_secret', 'totp_confirmed_at', 'totp_force']);
        });
    }
};
