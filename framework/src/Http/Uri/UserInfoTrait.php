<?php
declare(strict_types=1);
namespace DJWeb\Framework\Http\Uri;

trait UserInfoTrait
{
    private string $userInfo = '';
    public function getUserInfo(): string
    {
        return $this->userInfo;
    }
    public function withUserInfo(string $user, ?string $password = null): self
    {
        $new = clone $this;
        $new->userInfo = $password ? "$user:$password" : $user;
        return $new;
    }

    public function getAuthority(): string
    {
        $authority = $this->host;
        if ($this->userInfo) {
            $authority = $this->userInfo . '@' . $authority;
        }
        if ($this->port !== null) {
            $authority .= ':' . $this->port;
        }
        return $authority;
    }
}