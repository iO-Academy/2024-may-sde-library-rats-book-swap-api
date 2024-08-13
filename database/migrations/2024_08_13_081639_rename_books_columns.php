<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table){
            $table->renameColumn('claimed_by_name', 'name');
            $table->renameColumn('claimed_by_email', 'email');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table){
            $table->renameColumn('name', 'claimed_by_name');
            $table->renameColumn('email', 'claimed_by_email');
        });
    }
};
