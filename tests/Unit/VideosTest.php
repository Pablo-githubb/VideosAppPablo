<?php

namespace Tests\Unit;

use App\Models\Video;
use Carbon\Carbon;
use Tests\TestCase;

class VideosTest extends TestCase
{
    public function test_can_get_formatted_published_at_date(): void
    {
        Carbon::setLocale('ca');
        $video = new Video([
            'published_at' => Carbon::parse('2025-01-13 10:00:00'),
        ]);

        $this->assertEquals('13 de gener de 2025', $video->formatted_published_at);
    }

    public function test_can_get_formatted_published_at_date_when_not_published(): void
    {
        $video = new Video([
            'published_at' => null,
        ]);

        $this->assertEquals('', $video->formatted_published_at);
    }
}
