<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use App\Models\User;

class VerificationController extends Controller
{
    
    public function verify(Request $request) {
        $user = User::findOrFail($request->id);

        if (! hash_equals((string) $request->hash, sha1($user->getEmailForVerification()))) {
            return response()->json([
                "message" => "Unauthorized",
                "success" => false
            ], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                "message" => "User already verified!",
                "success" => false
            ]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json([
            'status' => 'success',
            "message" => "Email verified successfully!",
            "success" => true
        ]);
    }
}
