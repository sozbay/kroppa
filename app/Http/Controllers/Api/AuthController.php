<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AuthController extends AbstractApiController
{
    /**
     * Create User
     * @param Request $request
     * @return JsonResponse
     */
    public function createUser(Request $request): JsonResponse
    {
        try {
            $validateUser = Validator::make($request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required'
                ]);

            if ($validateUser->fails()) {
                return $this->response([],
                    401,
                    401,
                    errors: $validateUser->errors()->toArray());
            }

            /**
             * @var $user User
             */
            $user = User::query()->create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password'))
            ]);

            return $this->response([
                'user' => $user->toArray(),
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ]);

        } catch (Throwable $th) {
            return $this->response(
                [],
                500,
                500,
                errors: ['message' => $th->getMessage()]
            );
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return JsonResponse
     */
    public function loginUser(Request $request): JsonResponse
    {
        try {
            $validateUser = Validator::make($request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]);

            if ($validateUser->fails()) {
                return $this->response([],
                    401,
                    401,
                    errors: $validateUser->errors()->toArray());
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return $this->response(
                    [],
                    401,
                    401,
                    errors: [
                        'message' => 'Email & Password does not match with our record.'
                    ]
                );
            }

            $user = User::query()
                ->where('email', $request->get('email'))
                ->first();

            $token = $user->createToken('API TOKEN');
            $plainTextToken = explode('|', $token->plainTextToken)[1] ?? null;

            return $this->response([
                'user' => $user->toArray(),
                'token' => $plainTextToken
            ]);

        } catch (Throwable $th) {
            return $this->response(
                [],
                500,
                500,
                errors: ['message' => $th->getMessage()]
            );
        }
    }

    public function logout(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return $this->response(
                [],
                400,
                400,
                errors: ['message' => 'session is already closed']);
        }

        /**
         * @var $user User
         */
        $user = Auth::user();
        $user->tokens()->delete();
        $request->user()->currentAccessToken()->delete();

        return $this->response(['message' => 'Logout successfully!']);
    }
}
