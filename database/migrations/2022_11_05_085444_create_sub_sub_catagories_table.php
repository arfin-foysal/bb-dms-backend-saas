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
        Schema::create('sub_sub_catagories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('catagory_id');
            $table->unsignedBigInteger('sub_catagory_id')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['Pending', 'Active'])->default('pending');
            $table->enum('admin_status', ['Pending', 'Active'])->default('pending');
            
            $table->string('image')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('catagory_id')->references('id')->on('catagories')->onDelete('cascade');
            $table->foreign('sub_catagory_id')->references('id')->on('sub_catagories')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.

     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_sub_catagories');
    }
};
