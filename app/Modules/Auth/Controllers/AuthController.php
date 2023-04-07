<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\ApiController;
use App\Modules\Auth\DataProvider\AuthProvider;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Fractalistic\ArraySerializer;

class AuthController extends ApiController
{
    /**
     * Register user
     *
     * @param RegisterRequest $request
     * @param AuthProvider $provider
     * @return JsonResponse
     */
    public function register(RegisterRequest $request, AuthProvider $provider)
    {
        $validated = $request->validated();
        $user = $provider->create($validated);

        return $this->respondSuccess($user, 'registered');
    }

    /**
     * Login
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $tokenName = 'learning_laravel';

        if (Auth::attempt($validated)) {
            /** @var User $user */
            $user = auth()->user();

            # Revoke all existing tokens
            $user->revokeExistingTokensFor($tokenName);

            $token = $user->createToken($tokenName)->accessToken;

            return $this->respondSuccess([
                'user' => fractal($user, new UserTransformer())->toArray(),
                'token' => $token
            ]);
        }

        return $this->respondUnauthorized();
    }

    /**
     * Logout
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $token = $user->token();
        $token->revoke();

        return $this->respond('logged out', 204);
    }

    /**
     * Get me
     *
     * @return JsonResponse
     */
    public function getMe()
    {
        $user = fractal(auth()->user(), new UserTransformer())->toArray();
        return $this->respondSuccess($user, 'get me');
    }
}