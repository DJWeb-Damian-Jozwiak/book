<?php

namespace Tests\Models;

use DJWeb\Framework\Base\Application;
use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Query\InsertQueryBuilderContract;
use DJWeb\Framework\DBAL\Models\Factory;
use PDOStatement;
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
        $builder = $this->createMock(InsertQueryBuilderContract::class);
        $any = $this->any();
        $stmt = $this->createMock(PDOStatement::class);
        $builder->expects($any)->method('table')->willReturnSelf();
        $builder->expects($any)->method('values')->willReturnSelf();
        $builder->expects($any)->method('execute')->willReturn($stmt);
        $builder->expects($any)->method('getInsertId')->willReturn('1');
        $app->set(InsertQueryBuilderContract::class, $builder);

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