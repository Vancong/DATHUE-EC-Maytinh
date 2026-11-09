<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pure DDL Migration: Add direct stock management columns to products and orders
     * No data manipulation or implicit commit logic in this step.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'stock_quantity')) {
                $table->unsignedInteger('stock_quantity')->default(0)->after('cost_price');
            }
            if (! Schema::hasColumn('products', 'min_stock')) {
                $table->unsignedInteger('min_stock')->default(5)->after('stock_quantity');
            }
            if (! Schema::hasColumn('products', 'sold_quantity')) {
                $table->unsignedInteger('sold_quantity')->default(0)->after('min_stock');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'inventory_settled')) {
                $table->boolean('inventory_settled')->default(false)->after('cancelled_reason');
            }
            if (! Schema::hasColumn('orders', 'restocked_at')) {
                $table->timestamp('restocked_at')->nullable()->after('inventory_settled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('products', 'stock_quantity')) {
                $cols[] = 'stock_quantity';
            }
            if (Schema::hasColumn('products', 'min_stock')) {
                $cols[] = 'min_stock';
            }
            if (Schema::hasColumn('products', 'sold_quantity')) {
                $cols[] = 'sold_quantity';
            }
            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('orders', 'inventory_settled')) {
                $cols[] = 'inventory_settled';
            }
            if (Schema::hasColumn('orders', 'restocked_at')) {
                $cols[] = 'restocked_at';
            }
            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
