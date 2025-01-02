<?php

namespace App\Http\Middleware;

use App\Models\Comment;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Commentator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $commentatorId = Comment::findOrFail($request->id)->user_id;

        if (Auth::user()->id != $commentatorId) {
            return response()->json(['message' => 'data not found'], 404);
        }

        return $next($request);
    }
}
