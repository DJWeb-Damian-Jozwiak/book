<?php

declare(strict_types=1);

namespace DJWeb\Framework\Auth;

final readonly class PermissionManager
{
    public function __construct(private UserManager $userManager)
    {
    }

    public function hasPermission(string $permission): bool
    {
        return $this->userManager->check() && $this->userManager->user()?->hasPermission($permission);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        if (! $this->userManager->check()) {
            return false;
        }

        return array_any($permissions, fn (string $permission) => $this->hasPermission($permission));
    }

    public function hasAllPermissions(array $permissions): bool
    {
        if (! $this->userManager->check()) {
            return false;
        }

        return array_all($permissions, fn ($permission) => $this->hasPermission($permission));
    }

}
