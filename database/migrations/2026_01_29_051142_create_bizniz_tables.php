<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Products Table
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->unique(); // The Auto-Generated Code
            $table->string('name');
            $table->string('category')->index();
            $table->decimal('buy_price', 15, 2); // Precision for currency
            $table->decimal('sell_price', 15, 2);
            $table->integer('stock_quantity')->default(0);
            $table->string('image_path')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Safety: Don't actually delete data
        });

        // 2. Immutable Stock Audit Log (Safety & Forensics)
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained(); // Who did it?
            $table->string('type'); // 'in', 'out', 'adjustment', 'sale'
            $table->integer('quantity');
            $table->string('description')->nullable();
            $table->timestamp('created_at')->useCurrent(); // Immutable timestamp
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('products');
    }
};
