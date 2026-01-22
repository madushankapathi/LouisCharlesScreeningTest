<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class SetTenantDatabase
{
    public function handle(Request $request, Closure $next)
    {
        // Get tenant ID from header OR use default
        $tenantId = $request->header('X-Tenant-ID');

        if (!$tenantId) {
            // ✅ Fetch default tenant (replace 'Default Tenant' with your default name)
            $tenant = Tenant::where('name', 'Default Tenant')->first();
            if (!$tenant) {
                return response()->json([
                    'message' => 'Default tenant not found. Set a tenant with name "Default Tenant".'
                ], 400);
            }
        } else {
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                return response()->json(['message' => 'Tenant not found'], 400);
            }
        }

        if (empty($tenant->db_name)) {
            return response()->json(['message' => 'Tenant database not configured'], 400);
        }

        // ✅ Set tenant DB dynamically
        config(['database.connections.tenant.database' => $tenant->db_name]);
        DB::purge('tenant');
        DB::reconnect('tenant');

        return $next($request);
    }
}
