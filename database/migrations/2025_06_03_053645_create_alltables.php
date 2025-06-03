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
        Schema::create('abstract_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('password');
            $table->string('title');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number');
            $table->string('institution');
            $table->enum('contact_preference', ['email', 'phone number']);
            $table->string('address');
            $table->timestamps();
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('affiliation');
            $table->timestamps();
        });

        Schema::create('presenters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->timestamps();
        });


        Schema::create('abstract_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('abstract_account_id')->constrained()->onDelete('cascade');
            $table->foreignId('presenter_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('description');
            $table->string('file_directory');
            $table->enum('topic', ['Gastroentrology', 'hepatology', 'others(miscellaneous)']);
            $table->enum('presentation_type',['poster','oral']);
            $table->timestamps();
        });

        Schema::create('pivots', function (Blueprint $table) {
            $table->foreignId('abstract_paper_id')->constrained()->onDelete('cascade');
            $table->foreignId('author_id')->constrained()->onDelete('cascade');
        });

        Schema::create('event_accounts', function (Blueprint $table) {
            $table->id();
            $table->enum('category',
            ['Pedriatician','Other Specialist', 'General Practitioner',
             'Specialist Programmer (SP1)', 'Trainee (SP2)', 'Sub-Specialist Programme', 
             'APSPGHAN Member (International Participant)', 'International Participant', 
             'APSPGHAN Non-Member (Insternational Participant)']);

            $table->string('full_name');
            $table->string('nik_passport');
            $table->string('institution');
            $table->string('email');
            $table->string('phone_number');
            $table->string('address');
            $table->string('province_country');
            
            $table->timestamps();
        });

        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_account_id')->constrained()->onDelete('cascade');
            $table->string('pic_email');
            $table->string('pic_name');
            $table->string('pic_phone');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abstract_accounts');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('presenters');
        Schema::dropIfExists('abstracts');
        Schema::dropIfExists('pivots');
        Schema::dropIfExists('event_accounts');
        Schema::dropIfExists('sponsors');
    }
};
