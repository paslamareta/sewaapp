<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sewa_aplikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('hosting_id')->constrained()->onDelete('cascade');
            $table->string('domain');
            $table->decimal('biaya', 15, 2);
            $table->date('tanggal_mulai');
            $table->date('tanggal_expired');
            $table->enum('status', ['Expired', 'Belum Aktif', 'Aktif'])->default('Belum Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sewa_aplikasis');
    }
};
