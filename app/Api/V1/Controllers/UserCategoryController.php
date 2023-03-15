<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class UserCategoryController extends Controller
{
    /**
     *
     * @OA\Get(
     *   path="/api/user-categories",
     *   summary="list all user-category records",
     *   tags={"Category"},
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

        $userCategory = UserCategory::orderBy('created_at', 'desc')->where([
            ['user_id', $user->id],
        ]);

        $data = $userCategory->get()->toArray();

        return ResponseBuilder::asSuccess(200)->withData($data)->build();
    }

    /**
     *
     * @OA\Post(
     *   path="/api/user-categories",
     *   summary="Add user-category",
     *   tags={"Category"},
     *  @OA\RequestBody(
     *         required=true,
     *         description="user-category object",
     *         @OA\JsonContent(ref="#/components/schemas/UserCategoryRequest")
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
            'category_id' => 'required|string',
            'category_name' => 'required|string',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) return ResponseBuilder::error(422, [], $v->errors()->messages());

        $user = Auth::guard()->user();
        if (!$user) {
            return ResponseBuilder::asError(404)->withMessage('user_not_found')->build();
        }

        $inputs = $request->all();
        $inputs['user_id'] = $user->id;
        $userCategory = new UserCategory($inputs);

        if ($userCategory->save()) {
            return ResponseBuilder::success();
        }
        return ResponseBuilder::error(422, [], $v->errors()->messages());
    }

    /**
     *
     * @OA\Delete(
     *   path="/api/user-categories/{id}",
     *   summary="delete user-category record",
     *   tags={"Category"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="user-categories id"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="user-categories object",
     *         @OA\Parameter(name="auser_category_id"),
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
        $userCategory = UserCategory::find($id);
        if (!$userCategory) return ResponseBuilder::asError(404)->withMessage('userCategory not found')->build();

        $userCategory->delete();

        return ResponseBuilder::success();
    }
}
