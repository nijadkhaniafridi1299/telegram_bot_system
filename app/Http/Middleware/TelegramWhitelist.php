<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TelegramWhitelist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $clientIp = $request->getClientIp();

        if (config('app.env') == 'local' && $clientIp == '127.0.0.1') {
            return $next($request);
        }

        $whitelistedIpRanges = explode(',', config('telegram.whitelisted_ips'));

        foreach ($whitelistedIpRanges as $whitelistedIpRange) {
            if ($this->ipInRange($clientIp, $whitelistedIpRange)) {
                return $next($request);
            }
        }

        abort(401);
    }

    private function ipInRange(string $ip, string $range)
    {
        if (strpos($range, '/') == false) {
            $range .= '/32';
        }
        // $range is in IP/CIDR format eg 127.0.0.1/24
        list($range, $netmask) = explode('/', $range, 2);
        $range_decimal = ip2long($range);
        $ip_decimal = ip2long($ip);
        $wildcard_decimal = pow(2, (32 - (int)$netmask)) - 1;
        $netmask_decimal = ~$wildcard_decimal;
        return (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal));
    }
}
