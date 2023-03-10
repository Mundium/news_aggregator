<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/auth/login",
     * operationId="logIn",
     * tags={"Auth"},
     * summary="User log in",
     * description="User Log in here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"email", "password"},
     *               @OA\Property(property="email", type="email"),
     *               @OA\Property(property="password", type="password"),
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="Logged in Successfully",
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     *
     * Log the user in
     *
     */
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return ResponseBuilder::error(422, [], $v->errors()->messages());
        }

        $credentials = $request->only(['email', 'password']);

        try {
            $token = Auth::guard()->attempt($credentials);

            if(!$token) {
                return ResponseBuilder::error(403);
            }

        } catch (JWTException $e) {
            return ResponseBuilder::error(500);
        }
        $user = Auth::guard()->user();
        if($user->isActive == 0){
            Auth::logout();
            return ResponseBuilder::asError(403)->withMessage('user_is_inactive')->build();
        }
        $data = [
            'status' => 'ok',
            'token' => $token,
            'expires_in' => Auth::guard()->factory()->getTTL() * 60
        ];

        return ResponseBuilder::success($data);
    }
}
