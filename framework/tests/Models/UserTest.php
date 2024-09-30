<?php

namespace Tests\Models;

use DJWeb\Framework\DBAL\Models\Entities\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
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
            'created_at' => '2024-01-01 00:00:00',
        ]);
        $this->assertEquals(
            '2024-01-01 00:00:00',
            $user->created_at->toDateTimeString()
        );
    }

    public function testIsNew()
    {

    }
}