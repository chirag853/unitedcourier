<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add indexes on the foreign-key columns used by the customer "View All Shipments"
 * page queries to speed up lookups and eager-loading joins.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('shipper_info') && !$this->indexExists('shipper_info', 'shipper_info_customer_id_index')) {
            Schema::table('shipper_info', function (Blueprint $table) {
                $table->index('customer_id');
            });
        }

        if (Schema::hasTable('shipment_invoice') && !$this->indexExists('shipment_invoice', 'shipment_invoice_shipper_id_index')) {
            Schema::table('shipment_invoice', function (Blueprint $table) {
                $table->index('shipper_id');
            });
        }

        if (Schema::hasTable('shipment_invoice_items') && !$this->indexExists('shipment_invoice_items', 'shipment_invoice_items_invoice_id_index')) {
            Schema::table('shipment_invoice_items', function (Blueprint $table) {
                $table->index('invoice_id');
            });
        }

        if (Schema::hasTable('shipment_tracking') && !$this->indexExists('shipment_tracking', 'shipment_tracking_shipper_id_index')) {
            Schema::table('shipment_tracking', function (Blueprint $table) {
                $table->index('shipper_id');
            });
        }

        if (Schema::hasTable('consignee_info') && !$this->indexExists('consignee_info', 'consignee_info_shipper_id_index')) {
            Schema::table('consignee_info', function (Blueprint $table) {
                $table->index('shipper_id');
            });
        }

        if (Schema::hasTable('package_dimension') && !$this->indexExists('package_dimension', 'package_dimension_shipper_id_index')) {
            Schema::table('package_dimension', function (Blueprint $table) {
                $table->index('shipper_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipper_info', function (Blueprint $table) {
            $table->dropIndex(['customer_id']);
        });
        Schema::table('shipment_invoice', function (Blueprint $table) {
            $table->dropIndex(['shipper_id']);
        });
        Schema::table('shipment_invoice_items', function (Blueprint $table) {
            $table->dropIndex(['invoice_id']);
        });
        Schema::table('shipment_tracking', function (Blueprint $table) {
            $table->dropIndex(['shipper_id']);
        });
        Schema::table('consignee_info', function (Blueprint $table) {
            $table->dropIndex(['shipper_id']);
        });
        Schema::table('package_dimension', function (Blueprint $table) {
            $table->dropIndex(['shipper_id']);
        });
    }

    /**
     * Check whether an index with the given name already exists on the table.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        foreach (Schema::getIndexes($table) as $index) {
            if (($index['name'] ?? '') === $indexName) {
                return true;
            }
        }

        return false;
    }
};
