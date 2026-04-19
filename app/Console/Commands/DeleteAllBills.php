<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bill;
use App\Models\BillCompenzation;
use Illuminate\Support\Facades\DB;

class DeleteAllBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:delete-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Izbriše vse račune iz baze';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Brisanje vseh računov iz baze...');
        
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Delete all bills_compenzations first (pivot table)
        BillCompenzation::truncate();
        $this->info('Izbrisani vsi zapisi iz bills_compenzations.');
        
        // Delete all bills
        Bill::truncate();
        $this->info('Izbrisani vsi zapisi iz bills.');
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->info('Vsi računi so bili uspešno izbrisani iz baze.');
        
        return Command::SUCCESS;
    }
}
