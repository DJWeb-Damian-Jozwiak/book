<?php

namespace Tests\Models;

use DJWeb\Framework\Base\Application;
use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Models\Entities\User;
use DJWeb\Framework\DBAL\Query\Builders\DeleteQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\InsertQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\QueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\SelectQueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\UpdateQueryBuilder;
use PHPUnit\Framework\TestCase;
use Tests\Helpers\Models\Company;
use Tests\Helpers\Models\Post;

class UserTest extends TestCase
{
    private QueryBuilder $queryBuilder;
    private ConnectionContract $mockConnection;
    public function testPassword()
    {
        $user = new User();
        $user->password = 'password';
        $this->assertTrue(true);
        $this->assertTrue($user->verifyPassword('password'));
    }

    public function testHydrateDate()
    {
        $user = new User();
        $user->fill([
            'missing_property' => 1,
            'created_at' => '2024-01-01 00:00:00',
        ]);
        $this->assertEquals(
            '2024-01-01 00:00:00',
            $user->created_at->toDateTimeString()
        );
    }

    public function testIsNew()
    {
        $user = new User();
        $this->assertTrue($user->is_new);
    }

    public function testAfterInsertModelIsNotLongerNew()
    {
        $user = new User();
        $user->password = 'password';
        $mockPDOStatement = $this->createMock(\PDOStatement::class);
        $this->mockConnection->expects($this->once())
            ->method('query')
            ->willReturn($mockPDOStatement);
        $this->mockConnection->expects($this->once())
            ->method('getLastInsertId')
            ->willReturn('1');
        $user->save();
        $this->assertFalse($user->is_new);
        $this->assertEquals('1', $user->id);
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
                    'password' => '$argon2id$v=19',
                    'created_at' => '2024-01-01 00:00:00',
                ]
            ]);
        $this->mockConnection->expects($this->once())
            ->method('query')
            ->willReturn($mockPDOStatement);

        $query = User::query();
        $user = $query->select()->where('id', '=', '1')->first();
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('$argon2id$v=19', $user->password);
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
                    'password' => '$argon2id$v=19',
                    'created_at' => '2024-01-01 00:00:00',
                ]
            ]);
        $this->mockConnection->expects($this->once())
            ->method('query')
            ->willReturn($mockPDOStatement);

        $query = User::query();
        $user = $query->select()->where('id', '=', '1')->first();
        $user->password = 'password';
        $user->save();
    }

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
        $this->assertIsArray($company->users);
        //company users is cached, no second call!
        $this->assertInstanceOf(User::class, $company->users[0]);
    }

    public function testBelongsTo()
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
        $post = new Post();
        $post->user_id = 1;
        $this->assertInstanceOf(User::class, $post->user);
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
                    'password' => '$argon2id$v=19',
                    'created_at' => '2024-01-01 00:00:00',
                ]
            ]);
        $this->mockConnection->expects($this->once())
            ->method('query')
            ->willReturn($mockPDOStatement);

        $query = User::query();
        $users = $query->select()->where('id', '=', '1')->get();
        $this->assertIsArray($users);
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertEquals('$argon2id$v=19', $users[0]->password);
    }

    protected function setUp(): void
    {
        $this->mockConnection = $this->createMock(ConnectionContract::class);
        Application::getInstance()->set(
            InsertQueryBuilder::class,
            new InsertQueryBuilder($this->mockConnection)
        );
        Application::getInstance()->set(
            UpdateQueryBuilder::class,
            new UpdateQueryBuilder($this->mockConnection)
        );
        Application::getInstance()->set(
            DeleteQueryBuilder::class,
            new DeleteQueryBuilder($this->mockConnection)
        );
        Application::getInstance()->set(
            SelectQueryBuilder::class,
            new SelectQueryBuilder($this->mockConnection)
        );
        $this->queryBuilder = new QueryBuilder();
    }
}