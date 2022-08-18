<?php

namespace Kanakku\Http\Controllers\V1\Onboarding;

use Kanakku\Http\Controllers\Controller;
use Kanakku\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinishController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        \Storage::disk('local')->put('database_created', 'database_created');

        $user = User::where('role', 'super admin')->first();
        Auth::login($user);
    }
}
