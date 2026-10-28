<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user->role === 'parent') {
            return redirect()->route('espace-parent.index');
        }

        return redirect()->intended(config('fortify.home'));
    }
}
