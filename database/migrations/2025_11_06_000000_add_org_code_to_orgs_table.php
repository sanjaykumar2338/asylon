<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orgs', function (Blueprint $table): void {
            $table->string('org_code', 12)->nullable()->unique()->after('slug');
        });

        $this->populateOrgCodes();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orgs', function (Blueprint $table): void {
            $table->dropUnique(['org_code']);
            $table->dropColumn('org_code');
        });
    }

    /**
     * Assign unique report codes to existing organizations.
     */
    private function populateOrgCodes(): void
    {
        $orgs = DB::table('orgs')->select('id', 'org_code')->get();
        $existingCodes = $orgs->pluck('org_code')->filter()->values()->all();

        foreach ($orgs as $org) {
            if ($org->org_code) {
                continue;
            }

            $code = $this->generateUniqueCode($existingCodes);
            DB::table('orgs')
                ->where('id', $org->id)
                ->update(['org_code' => $code]);

            $existingCodes[] = $code;
        }
    }

    /**
     * Generate a unique alphanumeric organization code.
     */
    private function generateUniqueCode(array $existingCodes, int $length = 6): string
    {
        do {
            $code = Str::upper(Str::random($length));
        } while (in_array($code, $existingCodes, true));

        return $code;
    }
};
