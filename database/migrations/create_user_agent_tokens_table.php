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
        Schema::connection(config('vibes.database.connection'))->create('user_agent_tokens', function (Blueprint $table) {
            $table->id();
            $table->uuidMorphs('entity', 'entity');
            $table->uuid('token');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection(config('vibes.database.connection'))->dropIfExists('user_agent_tokens');
    }
};
