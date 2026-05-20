<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class VideosManageController extends Controller
{
    public function testedBy(): string
    {
        return \Tests\Feature\Videos\VideosManageControllerTest::class;
    }

    public function index(): View
    {
        Gate::authorize('videos_manage_index');

        $videos = Video::orderBy('published_at', 'desc')->get();

        return view('videos.manage.index', compact('videos'));
    }

    public function create(): View
    {
        Gate::authorize('videos_manage_create');

        return view('videos.manage.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('videos_manage_store');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'url' => 'required|url|max:255',
        ]);

        Video::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'url' => $validated['url'],
            'published_at' => now(),
        ]);

        return redirect()->route('videos.manage.index')
            ->with('success', 'Vídeo creat correctament.');
    }

    public function show(string $id): View
    {
        Gate::authorize('videos_manage_index');

        $video = Video::findOrFail($id);

        return view('videos.show', compact('video'));
    }

    public function edit(string $id): View
    {
        Gate::authorize('videos_manage_edit');

        $video = Video::findOrFail($id);

        return view('videos.manage.edit', compact('video'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        Gate::authorize('videos_manage_update');

        $video = Video::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'url' => 'required|url|max:255',
        ]);

        $video->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'url' => $validated['url'],
        ]);

        return redirect()->route('videos.manage.index')
            ->with('success', 'Vídeo actualitzat correctament.');
    }

    public function delete(string $id): View
    {
        Gate::authorize('videos_manage_delete');

        $video = Video::findOrFail($id);

        return view('videos.manage.delete', compact('video'));
    }

    public function destroy(string $id): RedirectResponse
    {
        Gate::authorize('videos_manage_destroy');

        $video = Video::findOrFail($id);
        $video->delete();

        return redirect()->route('videos.manage.index')
            ->with('success', 'Vídeo eliminat correctament.');
    }
}
