<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Server\SystemMessageModels as message;
use App\Http\Resources\Admin\SystemMessageCollection;

/**
 * 消息通知处理 class
 */
class MessageController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paginate = $request->paginate ?? 12;

        return new SystemMessageCollection(message::orderBy('id','DESC')->paginate($paginate));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user_id = $request->user_id ?? false;
        $content = $request->content ?? false;
        if (isset($user_id)) {
            $data = [
                'uid'   => $user_id,
                'content'   => $content,
                'type'   => 20,
            ];
            $this->api->addUsrMessage($user_id,2,$content);

            $model = message::create($data);

            return $this->success($model);
        }
        return $this->failed('failed');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
