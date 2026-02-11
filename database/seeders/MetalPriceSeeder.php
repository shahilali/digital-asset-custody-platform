<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Metal;
use App\Models\MetalPrice;
use Carbon\Carbon;

class MetalPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all metals
        $metals = Metal::all();

        if ($metals->isEmpty()) {
            $this->command->warn('No metals found. Please run MetalSeeder first.');
            return;
        }

        // Current approximate prices per kg (as of 2024)
        $currentPrices = [
            'XAU' => 62000.00,  // Gold
        ];

        foreach ($metals as $metal) {
            if (!isset($currentPrices[$metal->symbol])) {
                continue;
            }

            $basePrice = $currentPrices[$metal->symbol];

            // Create historical prices for the last 30 days
            for ($i = 30; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);

                // Add some random variation to simulate price fluctuations (±5%)
                $variation = (rand(-500, 500) / 10000); // -5% to +5%
                $price = $basePrice * (1 + $variation);

                MetalPrice::create([
                    'metal_id' => $metal->id,
                    'price_per_kg' => round($price, 2),
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }

            $this->command->info("Created 31 price records for {$metal->name} ({$metal->symbol})");
        }
    }
}
