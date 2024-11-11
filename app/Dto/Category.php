<?php

namespace App\Dto;

readonly class Category
{
    public function __construct(public string $name, public string $description)
    {
    }

    public static function fromCategory(\App\Database\Models\Category $category): Category
    {
        return new self($category->name, $category->description);
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}