<x-videos-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold">{{ $video->title }}</h2>
                    <p class="text-gray-500 text-sm mt-2">
                        Publicat el {{ $video->formatted_published_at }}
                        ({{ $video->formatted_for_humans_published_at }})
                    </p>

                    <div class="mt-6 aspect-w-16 aspect-h-9">
                        <iframe src="{{ str_replace('watch?v=', 'embed/', $video->url) }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-96"></iframe>
                    </div>

                    <div class="mt-6 prose">
                        <p>{{ $video->description }}</p>
                    </div>

                    <div class="mt-8 flex justify-between">
                        @if ($video->previous)
                            <a href="{{ route('videos.show', $video->previous) }}" class="text-indigo-600 hover:text-indigo-900">&larr; Vídeo anterior</a>
                        @else
                            <span></span>
                        @endif

                        @if ($video->next)
                            <a href="{{ route('videos.show', $video->next) }}" class="text-indigo-600 hover:text-indigo-900">Següent vídeo &rarr;</a>
                        @else
                            <span></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-videos-app-layout>
