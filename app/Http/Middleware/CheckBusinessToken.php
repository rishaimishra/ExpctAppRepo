<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class CheckBusinessToken
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = Auth::guard('business_api')->user();
            // dd($user);
            if (!$user) {
                throw new JWTException('Token not provided', 401);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token is Expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token is Invalid'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Authorization Token not found'], 401);
        }

        // Token is valid, proceed with the request
        return $next($request);
    }
}
