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
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke Users
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Foreign Key ke Items
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            
            $table->date('tgl_pinjam');
            $table->date('tgl_kembali');
            $table->integer('jumlah_pinjam');
            
            // Kolom untuk tugas Preprocessing
            $table->text('keterangan_asli');      // Input mentah user
            $table->text('keterangan_clean');     // Hasil preprocessing
            
            $table->enum('status', ['pending', 'disetujui', 'kembali'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
