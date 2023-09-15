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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('code',32)->unique();
            $table->string('company_name')->nullable();
            $table->string('shipper_name')->nullable();
            $table->string('shipper_company')->nullable();
            $table->string('shipper_phone',64)->nullable();
            $table->string('shipper_address')->nullable();
            $table->string('shipper_country')->nullable();
            $table->string('shipper_province')->nullable();
            $table->string('shipper_city')->nullable();
            $table->string('shipper_postal_code',64)->nullable();
            $table->string('shipper_email',128)->nullable();
            $table->string('shipper_tax_number_type',64)->nullable();
            $table->string('shipper_tax_number',64)->nullable();
            $table->string('shipper_id_card_number_type',64)->nullable();
            $table->string('shipper_id_card_number',64)->nullable();
            $table->string('ioss_number',64)->nullable();
            $table->string('ioss_issuer_country_code',64)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
