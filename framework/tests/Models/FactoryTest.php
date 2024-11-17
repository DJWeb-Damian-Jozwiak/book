<?php

namespace Tests\Models;

use DJWeb\Framework\Base\Application;
use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Models\Factory;
use PHPUnit\Framework\TestCase;
use Tests\BaseTestCase;
use Tests\Helpers\Casts\Status;
use Tests\Helpers\Models\Post;

class FactoryTest extends BaseTestCase
{
    public function testFactory()
    {
        $class = new class extends Factory
        {

            public function definition(): array
            {
                return [
                    'name' => 'test',
                    'status' => 'draft',
                ];
            }

            protected function getModelClass(): string
            {
                return Post::class;
            }
        };

        $app = Application::getInstance();
        $connection = $this->createMock(ConnectionContract::class);
        $connection->expects($this->exactly(3))->method('query')->willReturn(new \PDOStatement());
        $connection->expects($this->exactly(3))->method('getLastInsertId')->willReturn('1');
        $app->set(ConnectionContract::class, $connection);

        $factory = new $class();
        $post = $factory->create();
        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals(Status::draft, $post->status);

        $posts = $factory->createMany(2);
        $this->assertCount(2, $posts);
        $this->assertEquals(Status::draft, $posts[0]->status);
        $this->assertEquals(Status::draft, $posts[1]->status);
    }
}