<?php

namespace Tests\Models;

use DJWeb\Framework\DBAL\Models\Entities\User;
use PHPUnit\Framework\TestCase;
use Tests\Helpers\Casts\Status;
use Tests\Helpers\Models\Post;

class CastingTest extends TestCase
{
    public function testHydrateStatus()
    {
        $post = new Post();
        $post->fill([
            'status' => 'published',
        ]);
        $this->assertEquals(
            Status::published,
            $post->status
        );
    }
}