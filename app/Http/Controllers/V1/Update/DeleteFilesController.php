<?php

namespace Kanakku\Http\Controllers\V1\Update;

use Kanakku\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kanakku\Space\Updater;

class DeleteFilesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {

        if(isset($request->deleted_files) && !empty($request->deleted_files)) {
            Updater::deleteFiles($request->deleted_files);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
