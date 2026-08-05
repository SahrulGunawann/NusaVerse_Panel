<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the consolidated migrations for NusaVerse Controller.
     */
    public function up(): void
    {
        // 1. Users & Auth System
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->nullable()->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // 2. Cache Tables
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        // 3. Queue Jobs Tables
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // 4. Categories Table
        Schema::create('categories', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('icon')->default('account_balance');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 5. Provinces Table
        Schema::create('provinces', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('island')->nullable()->default('Indonesia');
            $table->timestamps();
        });

        // 6. Hotspots & Hotspot Items Tables
        Schema::create('hotspots', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('heritage_id')->nullable();
            $table->string('heritage_slug')->nullable();
            $table->string('title')->nullable();
            $table->timestamps();
        });

        Schema::create('hotspot_items', function (Blueprint $table) {
            $table->id();
            $table->string('hotspot_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->double('x')->default(0);
            $table->double('y')->default(0);
            $table->double('z')->default(0);
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('hotspot_id')->references('id')->on('hotspots')->onDelete('cascade');
        });

        // 7. Timelines & Timeline Events Tables
        Schema::create('timelines', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('heritage_id');
            $table->string('heritage_slug');
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('timeline_events', function (Blueprint $table) {
            $table->id();
            $table->string('timeline_id');
            $table->string('year');
            $table->string('title');
            $table->text('description');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 8. Heritages Table
        Schema::create('heritages', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category_id')->nullable();
            $table->string('category_name')->nullable();
            $table->string('province_id')->nullable();
            $table->string('province_name')->nullable();
            $table->text('short_description');
            $table->longText('full_description');
            $table->json('additional_sections')->nullable();
            $table->string('cover_image');
            $table->string('model_3d_url');
            $table->double('latitude');
            $table->double('longitude');
            $table->string('timeline_id')->default('');
            $table->string('hotspot_id')->default('');
            $table->string('opening_hours')->default('08.00 - 17.00 WIB');
            $table->string('ticket_price')->default('Gratis');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        // 9. Quizzes & Quiz Questions Tables
        Schema::create('quizzes', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('heritage_slug');
            $table->string('category');
            $table->string('title');
            $table->text('description');
            $table->integer('passing_score')->default(70);
            $table->timestamps();
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('quiz_id');
            $table->text('question');
            $table->json('options');
            $table->integer('correct_index');
            $table->text('explanation')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the consolidated migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('heritages');
        Schema::dropIfExists('timeline_events');
        Schema::dropIfExists('timelines');
        Schema::dropIfExists('hotspot_items');
        Schema::dropIfExists('hotspots');
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
