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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('version');
            $table->string('description')->nullable();
            $table->string('link')->nullable();
            $table->integer('host_id')->nullable();
            $table->string('status')->default('active'); // active, inactive, archived
            $table->string('type')->default('web'); // web, mobile, desktop
            $table->integer('created_by_id');
            $table->string('created_by_name');
            $table->integer('updated_by_id');
            $table->string('updated_by_name');
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->integer('created_by_id');
            $table->string('created_by_name');
            $table->integer('updated_by_id');
            $table->string('updated_by_name');
            $table->timestamps();
        });

        Schema::create(
            'customer_applications',
            function (Blueprint $table) {
                $table->id();
                $table->integer('customer_id');
                $table->integer('application_id');
                $table->integer('created_by_id');
                $table->string('created_by_name');
                $table->integer('updated_by_id');
                $table->string('updated_by_name');
                $table->timestamps();
            }
        );
        Schema::create('hosts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username');
            $table->string('url')->nullable();
            $table->string('type')->default('shared'); // shared, dedicated, cloud
            $table->string('status')->default('active'); // active, inactive
            $table->integer('created_by_id');
            $table->string('created_by_name');
            $table->integer('updated_by_id')->nullable();
            $table->string('updated_by_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
        Schema::dropIfExists('customer_applications');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('hosts');
    }
};
