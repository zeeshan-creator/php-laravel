<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class API_authentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Validate
        $validator = Validator::make($request->all(), [
            'api_token' => 'required|string',
        ]);
        if ($validator->fails()) {
            return  response()->json(['msg' => $validator->messages('*')->first()], 422);
        }

        // Check for Token if exists
        $token = $request->input('api_token');
        $user = DB::table('users')->select('id')->where('api_token', '=', hash('sha256', $token))->get();
        if ($user->isEmpty()) {
            return  response()->json(['msg' => 'The entered api_token doesn\'t exist'], 422);
        }
        $user_id = get_object_vars($user[0])['id'];
        $request->attributes->add(['user_id' => $user_id]);

        return $next($request);
    }
}
