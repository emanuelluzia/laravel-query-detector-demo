<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
     // (Intencionalmente com N+1)
    public function index()
    {
        $posts = Post::query()->latest()->paginate(50);

        // Simula acesso que dispara N+1 ao serializar
        $data = $posts->through(function ($p) {
            return [
                'id'        => $p->id,
                'title'     => $p->title,
                'author'    => $p->user->name,         // <- lazy load
                'comments'  => $p->comments->count(),  // <- lazy load
                'created_at'=> $p->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'total' => $posts->total(),
                'page'  => $posts->currentPage(),
            ]
        ]);
    }

    // Versão otimizada (com eager loading)
    public function optimized()
    {
        $posts = Post::with(['user','comments'])->latest()->paginate(50);

        $data = $posts->through(function ($p) {
            return [
                'id'        => $p->id,
                'title'     => $p->title,
                'author'    => $p->user->name,         // sem N+1
                'comments'  => $p->comments->count(),  // sem N+1
                'created_at'=> $p->created_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'total' => $posts->total(),
                'page'  => $posts->currentPage(),
            ]
        ]);
    }
}
