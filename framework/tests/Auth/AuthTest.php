<?php

namespace Tests\Auth;


use DJWeb\Framework\Auth\Auth;
use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\DBAL\Contracts\ConnectionContract;
use DJWeb\Framework\DBAL\Contracts\Query\SelectQueryBuilderContract;
use DJWeb\Framework\DBAL\Models\Entities\User;
use DJWeb\Framework\DBAL\Query\Builders\QueryBuilder;
use DJWeb\Framework\DBAL\Query\Builders\SelectQueryBuilder;
use DJWeb\Framework\Encryption\KeyGenerator;
use DJWeb\Framework\Storage\Session\Handlers\FileSessionHandler;
use DJWeb\Framework\Web\Application;
use PHPUnit\Framework\TestCase;
use Tests\BaseTestCase;

class AuthTest extends BaseTestCase
{
    private string $key = '';
    private QueryBuilder $queryBuilder;
    private ConnectionContract $mockConnection;
    public function testAttemptInvalidPassword()
    {
        $user = new User();
        $user->password = password_hash('password', PASSWORD_DEFAULT);
        $result = Auth::attempt($user, 'invalid_password');
        $this->assertFalse($result);
    }

    public function testUserIsInactive()
    {
        $user = new User();
        $user->password = password_hash('password', PASSWORD_DEFAULT);
        $user->is_active = false;
        $result = Auth::attempt($user, 'password');
        $this->assertFalse($result);
    }

    public function testValidUser()
    {
        $user = new User();
        $user->password = password_hash('password', PASSWORD_DEFAULT);
        $user->is_active = true;
        $user->id = 1;
        $result = Auth::attempt($user, 'password', remember: true);
        $this->assertTrue($result);
        Auth::logout();
    }

    public function testGetValidUser()
    {
        $user = new User();
        $user->password = password_hash('password', PASSWORD_DEFAULT);
        $user->is_active = true;
        $user->id = 1;
        Auth::attempt($user, 'password');
        $user2 = Auth::user();
        $this->assertEquals($user->id, $user2->id);
        Auth::logout();
    }

    public function testGetUserById()
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
        Application::getInstance()->session->set('auth_user_id', 1);
        $user = Auth::user();
        $this->assertEquals(1, $user->id);
        Auth::logout();
    }

    public function testGetByRememberToken()
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
        Application::getInstance()->cookies->set('remember_token', 'token');
        $user = Auth::user();
        $this->assertEquals(1, $user->id);
        Auth::logout();
    }

    public function testGetEmptyByRememberToken()
    {
        $mockPDOStatement = $this->createMock(\PDOStatement::class);
        $mockPDOStatement->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_ASSOC)
            ->willReturn([]);
        $this->mockConnection->expects($this->once())
            ->method('query')
            ->willReturn($mockPDOStatement);
        Application::getInstance()->cookies->set('remember_token', 'token');
        $user = Auth::user();
        $this->assertNull($user);
    }

    public function testGetEmptyUser()
    {
        $user = Auth::user();
        $this->assertNull($user);
        $this->assertTrue(Auth::guest());
        $this->assertFalse(Auth::check());
    }

    public function testEmptyUserCannotHavePermissions()
    {
        $this->assertFalse(Auth::hasPermission('admin'));
        $this->assertFalse(Auth::hasAllPermissions(['admin']));
        $this->assertFalse(Auth::hasAnyPermission(['admin']));
    }

    public function testEmptyUserCannotHaveRoles()
    {
        $this->assertFalse(Auth::hasRole('admin'));
        $this->assertFalse(Auth::hasAllRoles(['admin']));
        $this->assertFalse(Auth::hasAnyRole(['admin']));
    }

    protected function setUp(): void
    {
        Application::withInstance(null);
        $app = Application::getInstance();
        $this->key = new KeyGenerator()->generateKey();
        $app->bind('base_path', dirname(__DIR__, 2));
        $config = $this->createMock(ConfigContract::class);
        $dir = sys_get_temp_dir() . '/sessions_' . uniqid();
        $config->expects($this->any())->method('get')->willReturnCallback(fn(string $key) => match ($key) {
            'session.path' => $dir,
            'session.cookie_params' =>  [
                'lifetime' => 7200,
                'path' => null,
                'domain' => null,
                'secure' =>  isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'httponly' => true,
                'samesite' => 'Lax'
            ],
            'session.handler' => FileSessionHandler::class,
            'app.key' => $this->key,
            default => dd($key)
        });
        $this->mockConnection = $this->createMock(ConnectionContract::class);
        $app->set(
            SelectQueryBuilderContract::class,
            new SelectQueryBuilder($this->mockConnection)
        );
        $app->set(ConfigContract::class, $config);
    }
    protected function tearDown(): void
    {
        Auth::empty();
        $_COOKIE = [];
        Application::withInstance(null);
    }
}