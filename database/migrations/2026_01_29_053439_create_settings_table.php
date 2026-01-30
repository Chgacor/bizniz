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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer
            $table->string('group')->default('general'); // general, tax, contact
            $table->timestamps();
        });

        // Seed default settings immediately
        DB::table('settings')->insert([
            ['key' => 'business_name', 'value' => 'My Bizniz', 'group' => 'general'],
            ['key' => 'tax_rate', 'value' => '11', 'group' => 'tax'],
            ['key' => 'currency_symbol', 'value' => '$', 'group' => 'general'],
            ['key' => 'address', 'value' => '123 Startup Lane', 'group' => 'contact'],
        ]);
    }
};
