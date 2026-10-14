<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courier_services', function (Blueprint $table) {
            $table->string('tracking_link', 500)->nullable()->after('scode');
        });

        // One link per api_provider ...
        $map = [
            'ups' => 'https://www.ups.com/track',
            'gofo' => 'https://www.gofo.com/',
            'CIRRO PARCLL' => 'https://www.cirroparcel.com/',
            'primus' => 'https://www.fedex.com/en-us/tracking.html',
            'DHL (PLANS)' => 'https://www.dhl.com/global-en/home/tracking.html',
            'flyingtigers' => 'https://www.uniuni.com/',
            'AJ WORLDWIDE' => 'https://www.dpd.com/',
        ];
        foreach ($map as $apiProvider => $url) {
            DB::table('courier_services')
                ->whereRaw('LOWER(TRIM(api_provider)) = ?', [strtolower(trim($apiProvider))])
                ->update(['tracking_link' => $url]);
        }

        // DPD Germany rows (country DE) get the German DPD tracking link.
        DB::table('courier_services')
            ->whereRaw('LOWER(TRIM(api_provider)) = ?', ['aj worldwide'])
            ->whereRaw('UPPER(TRIM(country)) = ?', ['DE'])
            ->update(['tracking_link' => 'https://www.dpd.com/de/en/receiving/track/']);

        // Overseas covers many vendors — refine by method keyword so each
        // row gets its own vendor link instead of one blanket link.
        $overseasByMethod = [
            '%DPEX%' => 'https://www.dpex.com/',
            '%ARAMEX%' => 'https://www.aramex.com/in/en/track/results',
            '%CANADA%' => 'https://www.canadapost-postescanada.ca/track-reperage/en',
            '%AUSTRALIA%' => 'https://auspost.com.au/mypost/track',
        ];
        foreach ($overseasByMethod as $like => $url) {
            DB::table('courier_services')
                ->whereRaw('LOWER(TRIM(api_provider)) = ?', ['overseas'])
                ->where('method', 'like', $like)
                ->update(['tracking_link' => $url]);
        }
    }

    public function down(): void
    {
        Schema::table('courier_services', function (Blueprint $table) {
            $table->dropColumn('tracking_link');
        });
    }
};
