<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_hosting'); // varchar
            $table->text('url');            // text
            $table->boolean('active')->default(true); // boolean
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostings');
    }
};
