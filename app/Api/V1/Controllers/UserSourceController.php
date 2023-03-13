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
     * Display a listing of the resource.
     *
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
     * Store a newly created resource in storage.
     *
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserAuthor  $userAuthor
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