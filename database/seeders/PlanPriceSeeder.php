<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanPrice;
use Illuminate\Database\Seeder;

class PlanPriceSeeder extends Seeder
{
    public function run(): void
    {
        $core = Plan::where('slug', 'core')->firstOrFail();
        $pro = Plan::where('slug', 'pro')->firstOrFail();
        $apex = Plan::where('slug', 'apex')->firstOrFail();

        // CORE
        PlanPrice::updateOrCreate(
            ['plan_id' => $core->id, 'billing_interval' => 'monthly', 'is_early_adopter' => false],
            ['stripe_price_id' => 'price_1Sd56y12wg4WJNMBkLfLNmM2', 'is_active' => true]
        );
        PlanPrice::updateOrCreate(
            ['plan_id' => $core->id, 'billing_interval' => 'yearly', 'is_early_adopter' => false],
            ['stripe_price_id' => 'price_1Sd59g12wg4WJNMBsFwfdmJg', 'is_active' => true]
        );
        PlanPrice::updateOrCreate(
            ['plan_id' => $core->id, 'billing_interval' => 'monthly', 'is_early_adopter' => true],
            ['stripe_price_id' => 'price_1Sd5DV12wg4WJNMBPJxZJO36', 'is_active' => true]
        );
        PlanPrice::updateOrCreate(
            ['plan_id' => $core->id, 'billing_interval' => 'yearly', 'is_early_adopter' => true],
            ['stripe_price_id' => 'price_1Sd5E812wg4WJNMBuRXH4Zoi', 'is_active' => true]
        );

        // PRO
        PlanPrice::updateOrCreate(
            ['plan_id' => $pro->id, 'billing_interval' => 'monthly', 'is_early_adopter' => false],
            ['stripe_price_id' => 'price_1Sd5Fb12wg4WJNMBJWcLhGmB', 'is_active' => true]
        );
        PlanPrice::updateOrCreate(
            ['plan_id' => $pro->id, 'billing_interval' => 'yearly', 'is_early_adopter' => false],
            ['stripe_price_id' => 'price_1Sd5G712wg4WJNMB0xPJt7Y1', 'is_active' => true]
        );
        PlanPrice::updateOrCreate(
            ['plan_id' => $pro->id, 'billing_interval' => 'monthly', 'is_early_adopter' => true],
            ['stripe_price_id' => 'price_1Sd5GT12wg4WJNMBcGsXheQD', 'is_active' => true]
        );
        PlanPrice::updateOrCreate(
            ['plan_id' => $pro->id, 'billing_interval' => 'yearly', 'is_early_adopter' => true],
            ['stripe_price_id' => 'price_1Sd5Gm12wg4WJNMBjvpqHOrz', 'is_active' => true]
        );

        // APEX
        PlanPrice::updateOrCreate(
            ['plan_id' => $apex->id, 'billing_interval' => 'custom', 'is_early_adopter' => false],
            ['stripe_price_id' => 'price_1Sd5K812wg4WJNMBpHapkk2H', 'is_active' => false]
        );
    }
}
