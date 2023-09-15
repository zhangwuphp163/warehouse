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
        Schema::create('boxes', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id')->nullable();
            $table->integer('warehouse_id')->nullable();
            $table->string('code',32)->index();
            $table->string('name')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->decimal('length',6,1)->default(0.0)->comment("cm");
            $table->decimal('width',6,1)->default(0.0)->comment("cm");
            $table->decimal('height',6,1)->default(0.0)->comment("cm");
            $table->integer('weight')->default(0)->comment("g");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boxes');
    }
};
