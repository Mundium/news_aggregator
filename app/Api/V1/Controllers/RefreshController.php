<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;


class RefreshController extends Controller
{

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt.auth', []);
    }

    /**
     * @OA\Post(
     * path="/api/auth/refresh",
     * operationId="refresh",
     * tags={"Auth"},
     * security={{"bearerAuth":{}}},
     * summary="Refresh token",
     * description="refresh token",
     *      @OA\Response(
     *          response=200,
     *          description="token refreshed in Successfully",
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     * Refresh a token.
     *
     */
    public function refresh()
    {
        try {
            $token = Auth::guard()->refresh();
        } catch (JWTException $e) {
            return ResponseBuilder::error(500);
        }

        $data = [
            'status' => 'ok',
            'token' => $token,
            'expires_in' => Auth::guard()->factory()->getTTL() * 60
        ];

        return ResponseBuilder::success($data);
    }
}
