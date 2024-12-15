<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {Schema::create('sections', function (Blueprint $table) {
        $table->id();
        $table->string('sectionName');
        $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
        $table->timestamps();

    });

        \App\Models\Category::find(1)->sections()->create(['sectionName' => 'C++']);
        \App\Models\Category::find(1)->sections()->create(['sectionName' => 'Java']);
        \App\Models\Category::find(1)->sections()->create(['sectionName' => 'JavaScript']);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sections');
    }
};
