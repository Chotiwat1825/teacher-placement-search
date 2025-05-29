<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // เพิ่ม Carbon
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeenAt
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            // อัปเดต last_seen_at โดยไม่ trigger updated_at ของ User model (ถ้าไม่ต้องการ)
            // หรือจะใช้ $user->save() ก็ได้ถ้าต้องการให้ updated_at เปลี่ยนด้วย
            $user
                ->forceFill([
                    'last_seen_at' => Carbon::now(),
                ])
                ->saveQuietly(); // saveQuietly จะไม่ยิง model events
            // หรือ $user->last_seen_at = Carbon::now(); $user->save();
        }
        return $next($request);
    }
}
