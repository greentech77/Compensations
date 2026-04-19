<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DeleteEntitiesAndCompenzations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean-entities-compenzations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all entities (companies) and compensations from the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting deletion of entities and compensations...');

        $driver = DB::connection()->getDriverName();
        
        // Disable foreign key checks temporarily (MySQL/MariaDB)
        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($driver === 'pgsql') {
            // PostgreSQL doesn't need this, but we can disable triggers if needed
            // For now, we'll rely on CASCADE or delete in correct order
        }

        try {
            // Delete in order to respect foreign key constraints
            $this->truncateTableIfExists('bills_compenzations');
            $this->truncateTableIfExists('bills');
            $this->truncateTableIfExists('compenzation_entities');
            $this->truncateTableIfExists('compenzations_proposals');
            $this->truncateTableIfExists('implementation_agreement');
            $this->truncateTableIfExists('realization_agreement');
            $this->truncateTableIfExists('compenzations');
            $this->truncateTableIfExists('entities');

        } catch (\Exception $e) {
            $this->error('Error during deletion: ' . $e->getMessage());
            if (in_array($driver, ['mysql', 'mariadb'])) {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
            return Command::FAILURE;
        }

        // Re-enable foreign key checks (MySQL/MariaDB)
        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->info('');
        $this->info('✓ Successfully deleted all entities and compensations!');
        return Command::SUCCESS;
    }

    /**
     * Safely truncate a table if it exists
     *
     * @param string $tableName
     * @return void
     */
    private function truncateTableIfExists(string $tableName): void
    {
        if (Schema::hasTable($tableName)) {
            $this->info("Deleting {$tableName}...");
            DB::table($tableName)->truncate();
            $this->info("✓ Deleted {$tableName}");
        } else {
            $this->warn("⚠ Table {$tableName} does not exist, skipping...");
        }
    }
}

