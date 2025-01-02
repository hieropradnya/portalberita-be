<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    function store(Request $request)
    {
        // validasi dulu
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'comments_content' => 'required',
        ]);

        // ambil id pembuat komentar
        $request['user_id'] = Auth::user()->id;

        // simpan ke DB
        $comment = Comment::create($request->all());

        return new CommentResource($comment->loadMissing('commentator:id,username'));
    }

    function update(Request $request, $id)
    {
        $request->validate([
            'comments_content' => 'required',
        ]);

        // ambil data dari DB dan simpan ke variabel $comment
        $comment = Comment::findOrFail($id);

        // simpan update, data baru diambil dari request
        $comment->update($request->only('comments_content'));

        return new CommentResource($comment->loadMissing('commentator:id,username'));
    }

    function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        $comment->delete();

        return new CommentResource($comment->loadMissing('commentator:id,username'));
    }
}
