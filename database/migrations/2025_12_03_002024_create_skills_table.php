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
        $table->string('category');
        $table->unsignedTinyInteger('proficiency_level')->nullable(); // dependent
        $table->boolean('is_active')->default(true);
        $table->text('description')->nullable(); // markdown
        $table->json('attachments')->nullable();
        $table->json('tags')->nullable();
        $table->text('notes')->nullable();
        $table->timestamps();
        $table->softDeletes();
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
