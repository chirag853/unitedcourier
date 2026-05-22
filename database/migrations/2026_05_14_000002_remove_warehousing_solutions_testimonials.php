<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('warehousing_solutions_page')
            ->where('section', 'testimonials')
            ->delete();
    }

    public function down()
    {
        // No rollback because testimonial records are intentionally removed from warehousing_solutions_page.
    }
};
