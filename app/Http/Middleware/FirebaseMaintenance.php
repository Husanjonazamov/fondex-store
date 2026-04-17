<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\FirestoreHelper;

class FirebaseMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $maintenance_settings = FirestoreHelper::getDocument('settings/maintenance_settings');
            if (!empty($maintenance_settings)) {
                $isMaintenance = $maintenance_settings['isMaintenanceModeForVendor'] ?? false;
                if ($isMaintenance === true) {
                    return response()->view('maintenance');
                }
            }
        } catch (\Exception $e) {
            logger()->error("Maintenance check failed", ['error' => $e->getMessage()]);
        }
        
        return $next($request);
    }
}
