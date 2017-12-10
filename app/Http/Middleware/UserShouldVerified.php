<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class UserShouldVerified
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
      $response = $next($request);

      if (auth()->check() && !auth()->user()->is_verified) {
        auth()->logout();

        Session::flash("flash_notification", [
          "level" => "warning",
          "message" => "Akun Anda belum aktif. Silahkan klik pada link aktivasi yang telah kami kirim."
        ]);

        return redirect('/login');
      }

      return $response;
    }
}
