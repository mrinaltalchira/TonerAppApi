<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
class CheckTokenMiddleware  
{
    public function handle(Request $request, Closure $next)
    
    {

        
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Unauthorized', 'message' => 'Token not provided'], 401);
        }

        $user = User::where('token',$token)->first();
  
        
        if (!$user) {
            
            return response()->json(['error' => 'Unauthorized', 'message' => 'Invalid token'], 401);
        }

        $request->merge(['user' => $user]);

        return $next($request);
    }
}
