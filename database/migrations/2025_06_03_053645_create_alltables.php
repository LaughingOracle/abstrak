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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name');
            $table->date('deadline');
            $table->timestamps();
        });

        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('topic');
            $table->timestamps();
        });


        Schema::create('abstract_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->string('password');
            $table->string('title');
            $table->string('full_name');
            $table->string('username');
            $table->string('phone_number');
            $table->string('institution');
            $table->enum('contact_preference', ['email', 'phone number']);
            $table->string('address');
            $table->timestamps();
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('affiliation');
            $table->timestamps();
        });

        Schema::create('presenters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('abstract_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('event');
            $table->foreignId('abstract_account_id')->constrained()->onDelete('cascade');
            $table->foreignId('presenter_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('topic');
            $table->text('description');
            $table->string('reviewer')->nullable();
            $table->string('jury')->nullable();
            $table->string('logistic')->nullable();
            $table->enum('presentation_type',['poster','oral']);
            $table->enum('status',['pending','passed', 'failed'])->default('pending');
            $table->boolean('notified')->default(false); 
            $table->timestamps();
        });

        Schema::create('abstract_paper_author', function (Blueprint $table) {
            $table->foreignId('abstract_paper_id')->constrained()->onDelete('cascade');
            $table->foreignId('author_id')->constrained()->onDelete('cascade');
        });

        Schema::create('event_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->string('label');
            $table->string('html', 1024);
            
            $table->timestamps();
        });

        Schema::create('form_inputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_form_id')->constrained()->onDelete('cascade');
            $table->foreignId('abstract_paper_id')->constrained()->onDelete('cascade');
            $table->string('value');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abstract_papers');
        Schema::dropIfExists('topics');
        Schema::dropIfExists('presenters');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('abstract_accounts');
        Schema::dropIfExists('abstract_paper_author');
        Schema::dropIfExists('event_accounts');
        Schema::dropIfExists('sponsors');
        Schema::dropIfExists('events');
    }
};
