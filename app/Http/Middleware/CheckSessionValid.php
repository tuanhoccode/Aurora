<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Bỏ qua nếu đang ở route login/logout
            $exceptRoutes = ['login', 'logout', 'register', 'password.request', 'password.reset', 'verification.verify'];

            $currentRoute = $request->route()?->getName();
            if ($currentRoute && in_array($currentRoute, $exceptRoutes)) {
                return $next($request);
            }   

            $sessionId = session()->getId();
            $exists = DB::table('sessions')->where('id', $sessionId)
                ->where('user_id', Auth::id())->exists();

            if (!$exists) {
                Log::info('Session không tồn tại trong DB, sẽ logout người dùng:' . $sessionId);
                Auth::logout();
                session()->invalidate(); //Xóa session local
                session()->regenerateToken(); //đề phòng csrf
                return redirect()->route('login')->with('error', 'Phiên đăng nhập k còn hiệu lực.');
            }
        }
        return $next($request);
    }
}
