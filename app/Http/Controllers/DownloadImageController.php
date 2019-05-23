<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DownloadImageController extends Controller
{
    public function download(Request $request)
    {
        $input = $this->validate($request, [
            'message' => 'required',
            'data' => 'required'
        ]);
        return response()->download(storage_path("app/upload/{$input['data']['url']}"));
    }
}
