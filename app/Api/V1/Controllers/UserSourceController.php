<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class UserSourceController extends Controller
{
    /**
     *
     * @OA\Get(
     *   path="/api/user-sources",
     *   summary="list all user-source records",
     *   tags={"Source"},
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

        $userSource = UserSource::orderBy('created_at', 'desc')->where([
            ['user_id', $user->id],
        ]);

        $data = $userSource->get()->toArray();

        return ResponseBuilder::asSuccess(200)->withData($data)->build();
    }

    /**
     *
     * @OA\Post(
     *   path="/api/user-sources",
     *   summary="Add user-source",
     *   tags={"Source"},
     *  @OA\RequestBody(
     *         required=true,
     *         description="user-source object",
     *         @OA\JsonContent(ref="#/components/schemas/UserSourceRequest")
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
            'source_id' => 'required|string',
            'source_name' => 'required|string',
        ];

        $v = Validator::make($request->all(), $rules);
        if ($v->fails()) return ResponseBuilder::error(422, [], $v->errors()->messages());

        $user = Auth::guard()->user();
        if (!$user) {
            return ResponseBuilder::asError(404)->withMessage('user_not_found')->build();
        }

        $inputs = $request->all();
        $inputs['user_id'] = $user->id;
        $userSource = new UserSource($inputs);

        if ($userSource->save()) {
            return ResponseBuilder::success();
        }
        return ResponseBuilder::error(422, [], $v->errors()->messages());
    }

    /**
     *
     * @OA\Delete(
     *   path="/api/user-sources/{id}",
     *   summary="delete user-source record",
     *   tags={"Source"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="user-sources id"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="user-sources object",
     *         @OA\Parameter(name="auser_source_id"),
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
        $userSource = UserSource::find($id);
        if (!$userSource) return ResponseBuilder::asError(404)->withMessage('userSource not found')->build();

        $userSource->delete();

        return ResponseBuilder::success();
    }
}
