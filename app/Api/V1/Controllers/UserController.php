<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class UserController extends Controller
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt.auth', []);
    }

    /**
     * @OA\Get(
     *      path="/api/me",
     *      operationId="me",
     *      tags={"User"},
     *      security={{"bearerAuth":{}}},
     *      summary="Get Info Of Current User",
     *      description="Returns user's info",
     *      @OA\Response(
     *          response=200,
     *          description="ok",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *      ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request",
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found",
     *   ),
     *  )
     *
     */
    public function me()
    {
        $data = [
            'user' => $this->toArray(Auth::guard()->user())
        ];

        return ResponseBuilder::success($data);
    }

    /**
     * Transform the resource into an array.
     *
     * @param User $user
     * @return array
     */
    public function toArray(User $user)
    {
        return [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'isActive' => $user->isActive,
        ];
    }

    /**
     * @OA\Put(
     *   path="/api/users",
     *   summary="Update user",
     *   tags={"User"},
     *   @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User id"
     *     ),
     *   @OA\Response(response=200, description="successful operation"),
     *   @OA\Response(response=400, description="Validation errors"),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=403, description="Forbidden"),
     *   security={{"bearerAuth":{}}}
     *
     * )
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request)
    {
        $user = Auth::guard()->user();

        $rules = [
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'min:6',
            'first_name' => 'string|nullable',
            'last_name' => 'string|nullable',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) {
            return ResponseBuilder::error(400, [], $v->errors()->messages());
        }

        $input = $request->all();

        if (!$user) {
            return ResponseBuilder::asError(404)->withMessage('user_not_found')->build();

        }

        $user->update($input);
        if($user->save()){
            return ResponseBuilder::asSuccess(200)
                ->withData($this->toArray($user))
                ->withMessage('user_updated')
                ->build();
        } else {
            return ResponseBuilder::asError(500)->withMessage('user_not_updated')->build();
        }
    }

}
