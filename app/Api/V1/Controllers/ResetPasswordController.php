<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\MailResetPasswordToken;
use App\Models\ResetPassword as PasswordReset;
use App\Notifications\PasswordResetSuccess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use Tymon\JWTAuth\JWTAuth;

class ResetPasswordController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/auth/recovery",
     * operationId="sendResetEmail",
     * tags={"Auth"},
     * summary="reset password email",
     * description="send email to reset password",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"email"},
     *               @OA\Property(property="email", type="email"),
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="ok",
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function sendResetEmail(Request $request)
    {
        $rules = [
            'email' => 'required|email'
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return ResponseBuilder::error(422, [], $v->errors()->messages());
        }

        $user = User::where('email', $request->get('email'))->first();

        if(!$user) {
            return ResponseBuilder::asError(404)->withMessage('user_not_found')->build();
        }

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Str::random(60)
            ]
        );

        if ($user && $passwordReset)
            $user->notify(
                new MailResetPasswordToken($passwordReset->token)
            );

        return ResponseBuilder::asSuccess()->withMessage('We have e-mailed your password reset link!')->withData([
            'status' => 'ok'
        ])->build();
    }

    /**
     * @OA\Post(
     * path="/api/auth/reset",
     * operationId="resetPassword",
     * tags={"Auth"},
     * summary="Reset password",
     * description="reset password",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"token", "email", "password", "password_confirmation"},
     *               @OA\Property(property="email", type="email"),
     *               @OA\Property(property="password", type="password"),
     *               @OA\Property(property="password_confirmation", type="password"),
     *               @OA\Property(property="token", type="text"),
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="ok",
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function resetPassword(Request $request, JWTAuth $JWTAuth)
    {

        $rules = [
            'password' => 'required|string|confirmed',
            'token' => 'required|string'
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return ResponseBuilder::error(422, [], $v->errors()->messages());
        }
        $passwordReset = PasswordReset::where('token', $request->token)
            ->first();
        if (!$passwordReset)
            return ResponseBuilder::asError(404)->withMessage('This password reset token is invalid.')->build();


        if (Carbon::parse($passwordReset->updated_at)->addMinutes(config('auth.passwords.users.expire') * 60)->isPast()) {
            $passwordReset->delete();
            return ResponseBuilder::asError(404)->withMessage('This password reset token is expired.')->build();

        }

        $user = User::where('email', $passwordReset->email)->first();
        if (!$user)
            return ResponseBuilder::asError(404)->withMessage('We can\'t find a user with that e-mail address.')->build();

        $user->password = $request->password;
        $user->save();
        $passwordReset->delete();
        $user->notify(new PasswordResetSuccess($passwordReset));



        return ResponseBuilder::asSuccess()->withMessage('your password is changed')->withData([
            'status' => 'ok'
        ])->build();
    }

    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */
    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)
            ->first();
        if (!$passwordReset)
            return ResponseBuilder::asError(404)->withMessage('This password reset token is invalid.')->build();


        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return ResponseBuilder::asError(404)->withMessage('This password reset token is invalid.')->build();

        }
        return $passwordReset;
    }
}
