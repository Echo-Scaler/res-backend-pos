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
        Schema::create('dining_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('table_number');
            $table->string('name')->nullable();
            $table->unsignedSmallInteger('seating_capacity')->default(4);
            $table->string('floor_area')->default('Main Dining Hall');
            $table->string('status')->default('VACANT');
            $table->string('qr_token', 64)->unique();
            $table->foreignId('current_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['restaurant_id', 'table_number']);
            $table->index(['restaurant_id', 'status']);
            $table->index(['restaurant_id', 'floor_area']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dining_tables');
    }
};
