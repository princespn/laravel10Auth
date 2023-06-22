<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    use HttpResponses;

    public function Login(LoginUserRequest $request)
    {

        $request->validated($request->all());

        if (!Auth::attempt($request->only(['email', 'password']))) {

            return $this->error('', 'Credential do not match', 401);

        }

        $user = User::where('email', $request->email)->first();

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API Token of' . $user->name)->plainTextToken,

        ]);

    }

    public function Register(StoreUserRequest $request)
    {

        $request->validated($request->all());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->success([
            'user' => $user,
            'token' => $user->createToken('API Token of' . $user->name)->plainTextToken,

        ]);

    }

    public function Logout()
    {

        Auth::User()->currentAccessToken()->delete();

        return $this->success([
            'massage' => 'You have sucessfully logout',

        ]);
    }
}
