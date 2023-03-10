<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Requests\SignUpRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use Tymon\JWTAuth\JWTAuth;

class SignUpController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/auth/signup",
     * operationId="signUp",
     * tags={"Auth"},
     * summary="User Register",
     * description="User Register here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"first_name","email", "password", "last_name"},
     *               @OA\Property(property="first_name", type="text"),
     *               @OA\Property(property="email", type="email"),
     *               @OA\Property(property="password", type="password"),
     *               @OA\Property(property="last_name", type="text"),
     *               @OA\Property(property="phone", type="text"),
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Register Successfully",
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="Register Successfully",
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     *
     */
    public function signUp(Request $request, JWTAuth $JWTAuth)
    {
        $rules = [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required'
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return ResponseBuilder::error(422, [], $v->errors()->messages());
        }

        $input = $request->all();

        $user = new User($input);
        $user->save();
        if(!$user->save()) {
            return ResponseBuilder::asSuccess()->withHttpCode(500)->build();
        }

        if(!env('SIGN_UP_RELEASE_TOKEN', false)) {
            return ResponseBuilder::asSuccess()->withMessage('user_created')->withHttpCode(201)->build();
        }

        $token = $JWTAuth->fromUser($user);
        return ResponseBuilder::asSuccess()->withData([
            'token' => $token
        ])->withHttpCode(201)->build();
    }
}
