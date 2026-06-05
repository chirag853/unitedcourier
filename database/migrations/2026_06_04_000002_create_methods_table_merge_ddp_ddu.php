<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Create the new `methods` table with a method_type column
        Schema::create('methods', function (Blueprint $table) {
            $table->id();
            $table->enum('method_type', ['ddp', 'ddu'])->comment('DDP or DDU method type');
            $table->string('method_name', 100);
            $table->string('method_value', 100)->unique();
            $table->string('service_code', 10);
            $table->string('service_description', 100);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Step 2: Migrate data from ddp_methods (if table exists)
        if (Schema::hasTable('ddp_methods')) {
            $ddpRecords = DB::table('ddp_methods')->get();
            foreach ($ddpRecords as $record) {
                DB::table('methods')->insert([
                    'method_type'        => 'ddp',
                    'method_name'        => $record->method_name,
                    'method_value'       => $record->method_value,
                    'service_code'       => $record->service_code,
                    'service_description' => $record->service_description,
                    'sort_order'         => $record->sort_order,
                    'is_active'          => $record->is_active,
                    'created_at'         => $record->created_at,
                    'updated_at'         => $record->updated_at,
                ]);
            }
        }

        // Step 3: Migrate data from ddu_methods (if table exists)
        if (Schema::hasTable('ddu_methods')) {
            $dduRecords = DB::table('ddu_methods')->get();
            foreach ($dduRecords as $record) {
                DB::table('methods')->insert([
                    'method_type'        => 'ddu',
                    'method_name'        => $record->method_name,
                    'method_value'       => $record->method_value,
                    'service_code'       => $record->service_code,
                    'service_description' => $record->service_description,
                    'sort_order'         => $record->sort_order,
                    'is_active'          => $record->is_active,
                    'created_at'         => $record->created_at,
                    'updated_at'         => $record->updated_at,
                ]);
            }
        }

        // Step 4: Drop the old tables
        Schema::dropIfExists('ddp_methods');
        Schema::dropIfExists('ddu_methods');
    }

    public function down(): void
    {
        // Recreate ddp_methods
        Schema::create('ddp_methods', function (Blueprint $table) {
            $table->id();
            $table->string('method_name', 100);
            $table->string('method_value', 100)->unique();
            $table->string('service_code', 10);
            $table->string('service_description', 100);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Recreate ddu_methods
        Schema::create('ddu_methods', function (Blueprint $table) {
            $table->id();
            $table->string('method_name', 100);
            $table->string('method_value', 100)->unique();
            $table->string('service_code', 10);
            $table->string('service_description', 100);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Restore data from methods table
        if (Schema::hasTable('methods')) {
            $ddpRecords = DB::table('methods')->where('method_type', 'ddp')->get();
            foreach ($ddpRecords as $record) {
                DB::table('ddp_methods')->insert([
                    'method_name'        => $record->method_name,
                    'method_value'       => $record->method_value,
                    'service_code'       => $record->service_code,
                    'service_description' => $record->service_description,
                    'sort_order'         => $record->sort_order,
                    'is_active'          => $record->is_active,
                    'created_at'         => $record->created_at,
                    'updated_at'         => $record->updated_at,
                ]);
            }

            $dduRecords = DB::table('methods')->where('method_type', 'ddu')->get();
            foreach ($dduRecords as $record) {
                DB::table('ddu_methods')->insert([
                    'method_name'        => $record->method_name,
                    'method_value'       => $record->method_value,
                    'service_code'       => $record->service_code,
                    'service_description' => $record->service_description,
                    'sort_order'         => $record->sort_order,
                    'is_active'          => $record->is_active,
                    'created_at'         => $record->created_at,
                    'updated_at'         => $record->updated_at,
                ]);
            }
        }

        // Drop methods table
        Schema::dropIfExists('methods');
    }
};