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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId("project_id")->constrained()->onDelete("cascade");
            $table->foreignId("author_id")->constrained("users", "id")->onDelete("cascade");
            $table->foreignId("assignee_id")->constrained("users", "id")->onDelete("cascade");
            $table->string("title", 100);
            $table->string("description");
            $table->string("status");
            $table->integer("priority");
            $table->dateTime("due_date");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
