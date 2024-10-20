<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Relations;

use DJWeb\Framework\DBAL\Models\Attributes\BelongsTo as BelongsToAttribute;
use DJWeb\Framework\DBAL\Models\Attributes\HasMany as HasManyAttribute;
use DJWeb\Framework\DBAL\Models\Contracts\RelationContract;
use DJWeb\Framework\DBAL\Models\Model;

class RelationFactory
{
    public static function belongsTo(
        Model $parent,
        BelongsToAttribute $attribute,
    ): RelationContract {
        $relation = new RelationFactory()->create(
            'belongsTo',
            $parent,
            $attribute->related,
            $attribute->foreign_key,
            $attribute->local_key
        );
        $relation->addConstraints();
        return $relation;
    }

    public static function hasMany(
        Model $parent,
        HasManyAttribute $attribute,
    ): RelationContract {
        $relation = new RelationFactory()->create(
            'hasMany',
            $parent,
            $attribute->related,
            $attribute->foreign_key,
            $attribute->local_key
        );
        $relation->addConstraints();
        return $relation;
    }
    private function create(
        string $type,
        Model $parent,
        string $related,
        string $foreignKey,
        string $localKey
    ): RelationContract {
        return match ($type) {
            'hasMany' => new HasMany($parent, $related, $foreignKey, $localKey),
            'belongsTo' => new BelongsTo(
                $parent,
                $related,
                $foreignKey,
                $localKey
            ),
        };
    }
}
