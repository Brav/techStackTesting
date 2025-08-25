<?php

use App\Models\Database\Enums\CompetitorTypeEnum;
use App\Models\Database\Enums\EventStatusEnum;
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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->constrained('leagues');
            $table->string('name');
            $table->string('slug');
            $table->dateTime('scheduled_at');
            $table->enum('status_id', EventStatusEnum::values())
                ->default(EventStatusEnum::SCHEDULED->value);
            $table->enum('competitor_type_id', CompetitorTypeEnum::values())
                ->default(CompetitorTypeEnum::TEAM->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
