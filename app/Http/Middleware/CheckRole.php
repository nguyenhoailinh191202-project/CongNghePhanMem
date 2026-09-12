<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Chặn truy cập nếu vai trò của user không nằm trong danh sách cho phép.
     * Dùng: Route::middleware('role:admin,giang_vien')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->vai_tro, $roles, true)) {
            abort(403, 'Bạn không có quyền truy cập chức năng này.');
        }

        return $next($request);
    }
}
