<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            // Retail accounts
            [
                'account_number' => 'ACC-000001',
                'customer_name' => 'John Smith',
                'account_type' => 'retail',
            ],
            [
                'account_number' => 'ACC-000002',
                'customer_name' => 'Sarah Johnson',
                'account_type' => 'retail',
            ],
            [
                'account_number' => 'ACC-000003',
                'customer_name' => 'Michael Brown',
                'account_type' => 'retail',
            ],
            [
                'account_number' => 'ACC-000004',
                'customer_name' => 'Emily Davis',
                'account_type' => 'retail',
            ],
            [
                'account_number' => 'ACC-000005',
                'customer_name' => 'David Wilson',
                'account_type' => 'retail',
            ],

            // Institutional accounts
            [
                'account_number' => 'ACC-000006',
                'customer_name' => 'Goldman Sachs Trust',
                'account_type' => 'institutional',
            ],
            [
                'account_number' => 'ACC-000007',
                'customer_name' => 'JP Morgan Asset Management',
                'account_type' => 'institutional',
            ],
            [
                'account_number' => 'ACC-000008',
                'customer_name' => 'BlackRock Investment Fund',
                'account_type' => 'institutional',
            ],
            [
                'account_number' => 'ACC-000009',
                'customer_name' => 'Vanguard Precious Metals',
                'account_type' => 'institutional',
            ],
            [
                'account_number' => 'ACC-000010',
                'customer_name' => 'State Street Global Advisors',
                'account_type' => 'institutional',
            ],
        ];

        foreach ($accounts as $accountData) {
            $account = Account::create($accountData);
            $this->command->info("Created account: {$accountData['customer_name']} ({$account->account_number})");
        }
    }
}
