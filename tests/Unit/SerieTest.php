<?php

namespace Tests\Unit;

use App\Models\Serie;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SerieTest extends TestCase
{
    use RefreshDatabase;

    public function test_serie_have_videos(): void
    {
        $serie = Serie::create([
            'title' => 'Test Serie',
            'description' => 'This is a test serie description.',
            'user_name' => 'Test User',
        ]);

        $video1 = Video::create([
            'title' => 'Video 1',
            'description' => 'Description 1',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'series_id' => $serie->id,
        ]);

        $video2 = Video::create([
            'title' => 'Video 2',
            'description' => 'Description 2',
            'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'series_id' => $serie->id,
        ]);

        $this->assertCount(2, $serie->videos);
        $this->assertTrue($serie->videos->contains($video1));
        $this->assertTrue($serie->videos->contains($video2));
    }
}
