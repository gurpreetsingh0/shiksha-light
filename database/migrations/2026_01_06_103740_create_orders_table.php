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
    Schema::create('orders', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id');
      $table->foreign('user_id')
        ->on('users')
        ->references('id')
        ->onUpdate('cascade')
        ->onDelete('cascade');
      $table->string('name')->nullable();
      $table->string('email')->nullable();
      $table->string('mobile')->nullable();
      $table->string('address')->nullable();
      $table->string('city')->nullable();
      $table->string('pin_code')->nullable();
      $table->string('state')->nullable();
      $table->string('payment_type')->nullable(); //cod//gateway
      $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
      $table->string('payment_id')->nullable();
      $table->decimal('total_amount', 10, 2);
      $table->enum('order_status', [
        'pending',
        'confirmed',
        'shipped',
        'delivered',
        'cancelled'
      ])->default('pending');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('orders');
  }
};
