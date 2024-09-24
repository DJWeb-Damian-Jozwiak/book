<?php

namespace Tests\DBAL\Schema\MySQL;

class User
{
    public int $id {
        set => $this->id = $value;
        get => $this->id;
    }
}