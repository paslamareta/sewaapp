<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('broadcasts', function (Blueprint $table) {
        $table->id();
        $table->string('subject');   // judul email
        $table->text('message');     // isi email
        $table->timestamp('sent_at')->nullable(); // kapan dikirim
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broadcasts');
    }
};
