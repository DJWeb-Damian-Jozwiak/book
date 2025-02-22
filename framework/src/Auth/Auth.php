<?php

declare(strict_types=1);

namespace DJWeb\Framework\Auth;

use DJWeb\Framework\DBAL\Models\Entities\User;

final class Auth
{
    private static ?UserManager $userManager = null;
    private static ?RoleManager $roleManager = null;
    private static ?PermissionManager $permissionManager = null;

    public static function empty(): void
    {
        self::$userManager = null;
        self::$roleManager = null;
        self::$permissionManager = null;
    }

    public static function attempt(User $user, string $password, bool $remember = false): bool
    {
        return self::getUserManager()->attempt($user, $password, $remember);
    }

    public static function user(): ?User
    {
        return self::getUserManager()->user();
    }

    public static function check(): bool
    {
        return self::getUserManager()->check();
    }

    public static function guest(): bool
    {
        return self::getUserManager()->guest();
    }

    public static function login(User $user, bool $remember = false): void
    {
        self::getUserManager()->login($user, $remember);
    }

    public static function logout(): void
    {
        self::getUserManager()->logout();
    }

    public static function id(): int|string
    {
        return self::user()->id;
    }

    public static function hasRole(string $role): bool
    {
        return self::getRoleManager()->hasRole($role);
    }

    public static function hasAnyRole(array $roles): bool
    {
        return self::getRoleManager()->hasAnyRole($roles);
    }

    public static function hasAllRoles(array $roles): bool
    {
        return self::getRoleManager()->hasAllRoles($roles);
    }

    public static function hasPermission(string $permission): bool
    {
        return self::getPermissionManager()->hasPermission($permission);
    }

    public static function hasAnyPermission(array $permissions): bool
    {
        return self::getPermissionManager()->hasAnyPermission($permissions);
    }

    public static function hasAllPermissions(array $permissions): bool
    {
        return self::getPermissionManager()->hasAllPermissions($permissions);
    }

    private static function getUserManager(): UserManager
    {
        self::$userManager ??= new UserManager();
        return self::$userManager;
    }

    private static function getRoleManager(): RoleManager
    {
        return self::$roleManager ?? new RoleManager(self::getUserManager());
    }

    private static function getPermissionManager(): PermissionManager
    {
        return self::$permissionManager ?? new PermissionManager(self::getUserManager());
    }

}
