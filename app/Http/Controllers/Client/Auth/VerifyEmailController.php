<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $req):RedirectResponse
    {
        $user = User::findOrFail($req->route('id'));
        //Kiểm tra hash xác thực email
        if (! hash_equals((string) $req->route('hash'), sha1($user -> getEmailForVerification()))) {
            abort(403);
        }
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }
        Auth::login($user);
        return redirect()->route('home')->with('success', 'Email của bạn đã được xác thực!');
    }
}

