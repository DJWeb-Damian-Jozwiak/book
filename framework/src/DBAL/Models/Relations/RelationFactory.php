<?php

declare(strict_types=1);

namespace DJWeb\Framework\DBAL\Models\Relations;

use DJWeb\Framework\DBAL\Enums\RelationType;
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
            RelationType::belongsTo,
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
            RelationType::hasMany,
            $parent,
            $attribute->related,
            $attribute->foreign_key,
            $attribute->local_key
        );
        $relation->addConstraints();
        return $relation;
    }

    /**
     * @param RelationType $type
     * @param Model $parent
     * @param class-string<Model> $related
     * @param string $foreignKey
     * @param string $localKey
     *
     * @return RelationContract
     */
    private function create(
        RelationType $type,
        Model $parent,
        string $related,
        string $foreignKey,
        string $localKey
    ): RelationContract {
        return match ($type->value) {
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
