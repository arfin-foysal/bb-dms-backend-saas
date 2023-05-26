<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table ->string('name');
            $table ->string('email')->unique();
            $table ->string('image')->nullable();
            $table->string('number')->nullable();
            // $table ->string('company_unique_name')->unique();
            $table ->enum('status', ['Pending', 'Active'])->default('pending');
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
};
