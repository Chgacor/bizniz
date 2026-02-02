<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // Kita tambahkan kolom group, boleh null atau default 'general'
            // Kita letakkan setelah kolom 'value' biar rapi
            $table->string('group')->default('general')->after('value');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('group');
        });
    }
};
