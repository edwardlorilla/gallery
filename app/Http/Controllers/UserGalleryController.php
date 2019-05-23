<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Image;

class UserGalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $this->validate($request, [
            'title' => 'required|string|max:255',
            'info' => 'required|string|max:255',
            'file' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        if ($request->id && $file = $input['file']) {
            $name = time() . $file->getClientOriginalName();
            list($width, $height, $type, $attr) = getimagesize($file);

            $img = Image::make($file->getRealPath());
            $resize = $img->fit(round($width / 2), round($height / 2));
            $watermark = Image::make($file->getRealPath());
            $watermark->insert(Image::make(public_path('watermark.png'))->fit(round($width / 1.5), round($height / 1.5)), 'center');
            $path = "upload/100_{$name}";
            $resize->save(public_path($path));
            $file->storeAs('upload', $name); // without watermark
            $watermark->save("upload/{$name}");  // with watermark
            User::whereId($request->id)->first()->images()
                ->create([
                    'url' => $name,
                    'data' => [
                        'src' => "100_{$name}",
                        'title' => $input['title'],
                        'info' => $input['info'],
                        'type' => 'gallery',
                        'href' => NULL
                    ]
                ]);

        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json(\App\Image::whereImageableId($user->id)->whereImageableType(User::class)->where('data->type', 'gallery')->paginate());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
