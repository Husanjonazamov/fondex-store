<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckSubscriptionPlan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check if user is logged in
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check the 'issubscribed' field
            if ($user->isSubscribed=='false') {
                // Redirect to the subscription plan page
                return redirect()->route('subscription-plan.show');
            }
        }

        return $next($request);
    }
}
