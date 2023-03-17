<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserAuthor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class UserAuthorController extends Controller
{
    /**
     *
     * @OA\Get(
     *   path="/api/user-authors",
     *   summary="list all user-author records",
     *   tags={"Author"},
     *
     *   @OA\Response(response=200, description="successful operation"),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=404, description="Not found"),
     *   security={{"bearerAuth":{}}}
     *
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $user = Auth::guard()->user();
        if (!$user) {
            return ResponseBuilder::asError(404)->withMessage('user_not_found')->build();
        }

        $userAuthor = UserAuthor::orderBy('created_at', 'desc')->where([
            ['user_id', $user->id],
        ]);

        $data = $userAuthor->get()->toArray();

        return ResponseBuilder::asSuccess(200)->withData($data)->build();
    }

    /**
     *
     * @OA\Post(
     *   path="/api/user-authors",
     *   summary="Add user-author",
     *   tags={"Author"},
     *  @OA\RequestBody(
     *         required=true,
     *         description="user-author object",
     *         @OA\JsonContent(ref="#/components/schemas/UserAuthorRequest")
     *     ),
     *   @OA\Response(response=200, description="successful operation"),
     *   @OA\Response(response=400, description="Validation errors"),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=403, description="Forbidden"),
     *   security={{"bearerAuth":{}}}
     *
     * )
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'author_id' => 'required|string',
            'author_name' => 'required|string',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) return ResponseBuilder::error(422, [], $v->errors()->messages());

        $user = Auth::guard()->user();
        if (!$user) {
            return ResponseBuilder::asError(404)->withMessage('user_not_found')->build();
        }

        $inputs = $request->all();
        $inputs['user_id'] = $user->id;
        $userAuthor = new UserAuthor($inputs);

        if ($userAuthor->save()) {
            return ResponseBuilder::success($userAuthor->toArray());
        }
        return ResponseBuilder::error(422, [], $v->errors()->messages());
    }

    /**
     *
     * @OA\Delete(
     *   path="/api/user-authors/{id}",
     *   summary="delete user-authors record",
     *   tags={"Author"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="user-author id"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="user-author object",
     *         @OA\Parameter(name="auser_author_id"),
     *     ),
     *   @OA\Response(response=200, description="successful operation"),
     *   @OA\Response(response=400, description="Validation errors"),
     *   @OA\Response(response=401, description="Unauthenticated"),
     *   @OA\Response(response=403, description="Forbidden"),
     *   security={{"bearerAuth":{}}}
     *
     * )
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy($id)
    {
        $userAuthor = UserAuthor::find($id);
        if (!$userAuthor) return ResponseBuilder::asError(404)->withMessage('$userAuthor not found')->build();

        $userAuthor->delete();

        return ResponseBuilder::success();
    }
}
