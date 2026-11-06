<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konten', function (Blueprint $table) {
            $table->id('konten_id');
            $table->foreignId('admin_id')
                ->constrained('users', 'user_id', 'fk_konten_admin');
            $table->string('judul', 200);
            $table->string('slug', 220)->unique();
            $table->enum('jenis', ['artikel', 'edukasi', 'pengumuman', 'banner']);
            $table->string('ringkasan', 300)->nullable();
            $table->mediumText('isi');
            $table->string('gambar_url', 255)->nullable();
            $table->enum('status', ['draft', 'terbit', 'arsip'])->default('draft');
            $table->dateTime('published_at')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->index(['status', 'jenis', 'published_at'], 'idx_konten_publik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konten');
    }
};

