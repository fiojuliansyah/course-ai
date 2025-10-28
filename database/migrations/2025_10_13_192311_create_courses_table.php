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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('content');
            $table->unsignedInteger('price');
            $table->integer('students_count')->default(0);
            $table->enum('status', ['draft', 'active'])->default('draft');
            $table->string('thumbnail_path')->nullable();
            $table->string('syllabus_path')->nullable();
            
            $table->foreignId('category_id')
                  ->nullable() 
                  ->constrained('categories')
                  ->onDelete('set null'); 
                  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
