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
        Schema::create('books', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('author');
        $table->foreignId('category_id')->constrained()->onDelete('cascade');
        $table->decimal('price', 8, 2);      
        $table->text('description')->nullable();
        $table->integer('stock');
        $table->boolean('is_devotional')->default(false);
        $table->boolean('is_featured')->default(false);
        $table->string('cover_image')->nullable();
        $table->timestamps();

});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
