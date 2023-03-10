<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class LogoutController extends Controller
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
     * /**
     * @OA\Post(
     * path="/api/auth/logout",
     * operationId="logOut",
     * tags={"Auth"},
     * security={{"bearerAuth":{}}},
     * summary="User log out",
     * description="User log out here",
     *
     *      @OA\Response(
     *          response=401,
     *          description="Token has expired",
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="logged out Successfully",
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     *
     * Log the user out (Invalidate the token)
     *
     */
    public function logout()
    {
        Auth::guard()->logout();

        return ResponseBuilder::success(['message' => 'successfully_logged_out']);
    }
}
