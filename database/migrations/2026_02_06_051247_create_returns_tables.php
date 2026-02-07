<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Header Retur (returns)
        if (!Schema::hasTable('returns')) {
            Schema::create('returns', function (Blueprint $table) {
                $table->id();
                $table->string('return_code')->unique(); // Kode: RET-2026xxx
                $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users'); // Staff
                $table->text('reason')->nullable();
                $table->decimal('total_refund', 15, 2)->default(0);
                $table->timestamps();
            });
        }

        // 2. Tabel Detail Retur (return_items)
        if (!Schema::hasTable('return_items')) {
            Schema::create('return_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('return_id')->constrained('returns')->onDelete('cascade');
                $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
                $table->integer('quantity');
                $table->string('condition')->default('good'); // good/bad
                $table->decimal('refund_amount', 15, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('returns');
    }
};
