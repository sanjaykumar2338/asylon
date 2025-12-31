<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $globalRoles = ['super_admin', 'platform_admin'];
        $orgRoles = ['org_admin', 'executive_admin', 'security_lead', 'reviewer', 'org_user'];
        $driver = Schema::getConnection()->getDriverName();

        DB::table('users')
            ->whereIn('role', $globalRoles)
            ->update(['org_id' => null]);

        $firstOrgId = DB::table('orgs')->orderBy('id')->value('id');

        if ($firstOrgId) {
            DB::table('users')
                ->whereIn('role', $orgRoles)
                ->whereNull('org_id')
                ->update(['org_id' => $firstOrgId]);
        }

        if (in_array($driver, ['mysql', 'pgsql'], true)) {
            DB::statement("
                ALTER TABLE users
                ADD CONSTRAINT users_global_roles_require_null_org
                CHECK (
                    role IS NULL
                    OR role NOT IN ('super_admin','platform_admin')
                    OR org_id IS NULL
                )
            ");

            DB::statement("
                ALTER TABLE users
                ADD CONSTRAINT users_org_roles_require_org
                CHECK (
                    role IS NULL
                    OR role NOT IN ('org_admin','executive_admin','security_lead','reviewer','org_user')
                    OR org_id IS NOT NULL
                )
            ");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'pgsql'], true)) {
            try {
                DB::statement('ALTER TABLE users DROP CHECK users_global_roles_require_null_org');
            } catch (\Throwable) {
                //
            }

            try {
                DB::statement('ALTER TABLE users DROP CHECK users_org_roles_require_org');
            } catch (\Throwable) {
                //
            }
        }
    }
};
