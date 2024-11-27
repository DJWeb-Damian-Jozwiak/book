<?php

declare(strict_types=1);

namespace App\Dto;

use JsonSerializable;

readonly class Category implements JsonSerializable
{
    public function __construct(public string $name, public string $description,public int $id)
    {
    }

    public static function fromCategory(\App\Database\Models\Category $category): Category
    {
        return new self($category->name, $category->description, $category->id);
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}