<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'ASYLON CORE',
                'slug' => 'core',
                'max_users' => 25,
                'max_reports_per_month' => 500,
                'trial_days' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'ASYLON PRO',
                'slug' => 'pro',
                'max_users' => null,
                'max_reports_per_month' => null,
                'trial_days' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'ASYLON APEX',
                'slug' => 'apex',
                'max_users' => null,
                'max_reports_per_month' => null,
                'trial_days' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
