<?php

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
use Tests\Helpers\Models\Role;
use Tests\Helpers\Models\User;

class BelongsToManyTest extends BaseTestCase
{
    private QueryBuilder $queryBuilder;
    private ConnectionContract $mockConnection;

    public function testBelongsToMany()
    {
        $mockPDOStatement = $this->createMock(\PDOStatement::class);
        $mockPDOStatement->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_ASSOC)
            ->willReturn([
                [
                    'id' => 1,
                    'name' => 'Admin',
                    'created_at' => '2024-01-01 00:00:00',
                    'pivot_role_id' => 1,
                    'pivot_user_id' => 1,
                ],
                [
                    'id' => 2,
                    'name' => 'Editor',
                    'created_at' => '2024-01-01 00:00:00',
                    'pivot_role_id' => 2,
                    'pivot_user_id' => 1,
                ]
            ]);

        $this->mockConnection->expects($this->once())
            ->method('query')
            ->willReturn($mockPDOStatement);

        $user = new User();
        $user->id = 1;

        $roles = $user->roles;
        $this->assertIsArray($roles);
        $this->assertCount(2, $roles);
        $this->assertInstanceOf(Role::class, $roles[0]);
        $this->assertEquals('Admin', $roles[0]->name);
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