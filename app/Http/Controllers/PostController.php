<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PostDetailResource;

class PostController extends Controller
{
    function index()
    {
        // $posts = Post::all();
        // return response()->json([
        //     'data' => $posts
        // ], 200);

        return PostDetailResource::collection(Post::all()->loadMissing('writer:id,username'));
    }

    function show($id)
    {
        $post = Post::with(['writer:id,username', 'comments:id,post_id,user_id,comments_content'])->findOrFail($id);
        return new PostDetailResource($post);
    }

    function store(Request $request)
    {
        // validasi dulu
        $request->validate([
            'title' => 'required|max:255',
            'news_content' => 'required',
        ]);

        // jika ada gambar
        if ($request->file) {
            // beri nama file dg random dan ambil ekstensi file yang dikirim
            $fileName = $this->generateRandomString();
            $extension = $request->file->extension();

            //kita pindahkan ke storage dengan folder imageBerita
            Storage::putFileAs('imageBerita', $request->file, $fileName . '.' . $extension);

            // tambahkan ke $request untuk diinputkan nama file ke DB
            $request['image'] = $fileName . '.' . $extension;
        }

        // simpan id user, tambahkan ke request 
        $request['author'] = Auth::user()->id;

        // simpan ke database
        $post = Post::create($request->all());
        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    function update(Request $request, $id)
    {
        // validasi dulu
        $request->validate([
            'title' => 'required|max:255',
            'news_content' => 'required',
        ]);

        // ambil data dari DB dan simpan ke variabel $post
        $post = Post::findOrFail($id);

        // simpan update, data baru diambil dari request
        $post->update($request->all());

        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    function destroy($id)
    {
        $post = Post::findOrFail($id);

        $post->delete();

        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    function generateRandomString($length = 30)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
