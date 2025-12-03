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
    Schema::create('skills', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique();
        $table->foreignId('category_id')->constrained()->cascadeOnDelete();
        $table->unsignedTinyInteger('proficiency_level')->nullable();
        $table->boolean('is_active')->default(true);
        $table->text('description')->nullable();
        $table->json('attachments')->nullable();
        $table->json('tags')->nullable();
        $table->text('notes')->nullable();
        $table->boolean('archived')->default(false);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
