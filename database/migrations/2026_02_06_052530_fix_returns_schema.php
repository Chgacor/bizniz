<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. HAPUS TABEL LAMA (Jaga-jaga kalau sudah dibuat di migrasi lain)
        // Kita pakai foreign key check = 0 biar bisa force drop
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('returns');
        Schema::enableForeignKeyConstraints();

        // 2. BUAT ULANG TABEL HEADER (returns)
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_code')->unique(); // Kode Retur
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->text('reason')->nullable();
            $table->decimal('total_refund', 15, 2)->default(0); // Kolom Total
            $table->timestamps();
        });

        // 3. BUAT ULANG TABEL DETAIL (return_items)
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained('returns')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->integer('quantity');
            $table->string('condition')->default('good');

            // INI KOLOM YANG DICARI-CARI DARI TADI
            $table->decimal('refund_amount', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('returns');
        Schema::enableForeignKeyConstraints();
    }
};
