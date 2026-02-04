<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. TABEL PROMOSI (Requirement #5)
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['service', 'product', 'transaction']); // Lingkup promo
            $table->enum('discount_type', ['fixed', 'percentage']);
            $table->decimal('value', 15, 2); // Nilai diskon (Rp atau %)
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. TABEL PEMBELIAN / PURCHASE INVOICE (Requirement #2)
        // Stok masuk HARUS lewat sini, tidak boleh edit manual.
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // Nomor Nota Supplier
            $table->string('supplier_name');
            $table->date('invoice_date');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->foreignId('user_id')->constrained('users'); // Siapa yang input
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_invoice_id')->constrained('purchase_invoices')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity');
            $table->decimal('buy_price', 15, 2); // Harga beli saat itu (History Harga)
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });

        // 3. TABEL RETUR PENJUALAN (Requirement #3)
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_code')->unique();
            $table->foreignId('transaction_id')->constrained('transactions'); // Wajib link ke Transaksi Asli
            $table->foreignId('user_id')->constrained('users'); // Siapa yang memproses
            $table->text('reason')->nullable();
            $table->decimal('total_refund', 15, 2);
            $table->timestamps();
        });

        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained('returns')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity');
            $table->enum('condition', ['good', 'bad'])->default('good'); // Good = Masuk stok lagi, Bad = Buang
            $table->timestamps();
        });

        // 4. TABEL KATEGORI (Requirement #4 - Dropdown)
        // Kita buat tabel terpisah agar dropdown konsisten
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Seed kategori default
        DB::table('categories')->insert([
            ['name' => 'Sparepart'],
            ['name' => 'Oli'],
            ['name' => 'Jasa']
        ]);

        // 5. UPDATE TABEL SETTINGS (Requirement #7)
        // Pastikan key-key penting ada. Kita pakai seeder atau updateOrCreate nanti.
        // Struktur tabel settings diasumsikan sudah ada (key, value).
    }

    public function down(): void
    {
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('returns');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchase_invoices');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('categories');
    }
};
