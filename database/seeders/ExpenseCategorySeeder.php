<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Bricks', 'description' => 'Bricks, blocks, and related masonry materials'],
            ['name' => 'Cement', 'description' => 'Cement bags and related bonding mixtures'],
            ['name' => 'Sand', 'description' => 'Construction sand'],
            ['name' => 'Crush', 'description' => 'Crushed stones and gravel (Bajri)'],
            ['name' => 'Sariya / Steel', 'description' => 'Reinforcement steel bars (Sariya)'],
            ['name' => 'Wood', 'description' => 'Timber and wooden materials'],
            ['name' => 'Doors', 'description' => 'Doors and door frames'],
            ['name' => 'Windows', 'description' => 'Windows and window frames'],
            ['name' => 'Electrical', 'description' => 'Wiring, conduits, switches, lights, and electrical fittings'],
            ['name' => 'Plumbing', 'description' => 'Piping, sanitaryware, fittings, and plumbing materials'],
            ['name' => 'Labor', 'description' => 'General laborers and helpers payments'],
            ['name' => 'Mason', 'description' => 'Skilled masons (Raj Mistry) payments'],
            ['name' => 'Contractor', 'description' => 'Contractor payments and milestones'],
            ['name' => 'Demolition', 'description' => 'Demolition work for old structures'],
            ['name' => 'Transportation', 'description' => 'Freight charges, loading, and unloading'],
            ['name' => 'Machinery', 'description' => 'Mixer machine rental, tools, and equipment charges'],
            ['name' => 'Water', 'description' => 'Water tankers and supply for curing and construction'],
            ['name' => 'Electricity', 'description' => 'Temporary connection, meters, and bills during construction'],
            ['name' => 'Other', 'description' => 'Miscellaneous expenses not listed elsewhere'],
        ];

        foreach ($categories as $category) {
            DB::table('expense_categories')->updateOrInsert(
                ['name' => $category['name']],
                ['description' => $category['description'], 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
