<?php

declare(strict_types=1);

namespace Tests\Models;

use DJWeb\Framework\Base\Application;
use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Query\DeleteQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\InsertQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\SelectQueryBuilderContract;
use DJWeb\Framework\DBAL\Contracts\Query\UpdateQueryBuilderContract;
use DJWeb\Framework\DBAL\Query\Builders\DeleteQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\InsertQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\QueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\SelectQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\UpdateQueryBuilder;
use Tests\BaseTestCase;
use Tests\Helpers\Casts\Status;
use Tests\Helpers\Models\Post;

class ModelTest extends BaseTestCase
{
    private QueryBuilder $queryBuilder;
    private ConnectionContract $mockConnection;

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

    public function testIsNew()
    {
        $post = new Post();
        $this->assertTrue($post->is_new);
    }

    public function testAfterInsertModelIsNotLongerNew()
    {
        $post = new Post();
        $post->status = Status::published;
        $mockPDOStatement = $this->createMock(\PDOStatement::class);
        $this->mockConnection->expects($this->once())
            ->method('query')
            ->willReturn($mockPDOStatement);
        $this->mockConnection->expects($this->once())
            ->method('getLastInsertId')
            ->willReturn('1');
        $post->save();
        $this->assertFalse($post->is_new);
        $this->assertEquals('1', $post->id);
    }

    public function testFirst()
    {

        $mockPDOStatement = $this->createMock(\PDOStatement::class);
        $mockPDOStatement->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_ASSOC)
            ->willReturn([
                [
                    'id' => 1,
                    'status' => 'published',
                    'created_at' => '2024-01-01 00:00:00',
                ]
            ]);
        $this->mockConnection->expects($this->once())
            ->method('query')
            ->willReturn($mockPDOStatement);

        $query = Post::query();
        $post = $query->select()->where('id', '=', '1')->first();
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testUpdate()
    {

        $mockPDOStatement = $this->createMock(\PDOStatement::class);
        $mockPDOStatement->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_ASSOC)
            ->willReturn([
                [
                    'id' => 1,
                    'status' => 'published',
                    'created_at' => '2024-01-01 00:00:00',
                ]
            ]);
        $this->mockConnection->expects($this->once())
            ->method('query')
            ->willReturn($mockPDOStatement);

        $query = Post::query();
        $post = $query->select()->where('id', '=', '1')->first();
        $post->status = Status::draft;
        $post->save();
    }

    public function testGet()
    {

        $mockPDOStatement = $this->createMock(\PDOStatement::class);
        $mockPDOStatement->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_ASSOC)
            ->willReturn([
                [
                    'id' => 1,
                    'status' => 'published',
                    'created_at' => '2024-01-01 00:00:00',
                ]
            ]);
        $this->mockConnection->expects($this->once())
            ->method('query')
            ->willReturn($mockPDOStatement);

        $query = Post::query();
        $posts = $query->select()->where('id', '=', '1')->get();
        $this->assertIsArray($posts);
        $this->assertInstanceOf(Post::class, $posts[0]);
    }

    protected function setUp(): void
    {
        $this->mockConnection = $this->createMock(ConnectionContract::class);
        Application::getInstance()->set(
            InsertQueryBuilderContract::class,
            new InsertQueryBuilder($this->mockConnection)
        );
        Application::getInstance()->set(
            UpdateQueryBuilderContract::class,
            new UpdateQueryBuilder($this->mockConnection)
        );
        Application::getInstance()->set(
            DeleteQueryBuilderContract::class,
            new DeleteQueryBuilder($this->mockConnection)
        );
        Application::getInstance()->set(
            SelectQueryBuilderContract::class,
            new SelectQueryBuilder($this->mockConnection)
        );
        $this->queryBuilder = new QueryBuilder();
    }
}