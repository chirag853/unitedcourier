<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteTestCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaigns:delete-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete test campaign records from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // List of campaign names to delete from campaign-archieve.js
        $campaignNamesToDelete = [
            'Distribution',
            'Merchandising',
            'Pricing',
            'Increased sales',
        ];

        try {
            // Check if the table has a 'name' column
            $columns = DB::getSchemaBuilder()->getColumnListing('campaigns');
            
            if (in_array('name', $columns)) {
                // Delete campaigns by name
                $deleted = DB::table('campaigns')
                    ->whereIn('name', $campaignNamesToDelete)
                    ->delete();

                $this->info("Successfully deleted {$deleted} test campaign records.");
            } else {
                $this->warn('The campaigns table does not have a "name" column.');
                $this->info('Current columns: ' . implode(', ', $columns));
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
