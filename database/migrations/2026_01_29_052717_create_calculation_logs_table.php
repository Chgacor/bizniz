<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calculation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable(); // e.g., "Tax Calculation Jan"
            $table->string('expression'); // e.g., "5000 * 12 + 200"
            $table->decimal('result', 20, 2);
            $table->string('context_tag')->default('general'); // 'pricing', 'tax', 'wage'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculation_logs');
    }
};
