<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
