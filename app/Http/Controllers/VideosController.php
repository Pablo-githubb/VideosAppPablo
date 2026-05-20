<?php

namespace App\Http\Controllers;

use App\Models\Video;

class VideosController extends Controller
{
    public function testedBy(): string
    {
        return \Tests\Feature\Videos\VideosTest::class;
    }

    public function index()
    {
        $videos = Video::orderBy('published_at', 'desc')->get();

        return view('videos.index', compact('videos'));
    }

    public function show(string $id)
    {
        $video = Video::findOrFail($id);

        return view('videos.show', compact('video'));
    }
}
