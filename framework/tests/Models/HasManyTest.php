<?php

declare(strict_types=1);

namespace Models;

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
use Tests\Helpers\Models\Company;
use Tests\Helpers\Models\Post;

class HasManyTest extends BaseTestCase
{
    private QueryBuilder $queryBuilder;
    private ConnectionContract $mockConnection;

    public function testHasMany()
    {
        $mockPDOStatement = $this->createMock(\PDOStatement::class);
        $mockPDOStatement->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_ASSOC)
            ->willReturn([
                [
                    'id' => 1,
                    'created_at' => '2024-01-01 00:00:00',
                ]
            ]);
        $this->mockConnection->expects($this->once())
            ->method('query')
            ->willReturn($mockPDOStatement);
        $company = new Company();
        $company->id = 1;
        $this->assertIsArray($company->posts);

        $this->assertInstanceOf(Post::class, $company->posts[0]);
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