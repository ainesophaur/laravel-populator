<?php

namespace Guava\LaravelPopulator\Concerns\Pipe;

use Guava\LaravelPopulator\Concerns\Pipe\Relations\BelongsRelations;
use Guava\LaravelPopulator\Concerns\Pipe\Relations\HasOneOrManyRelations;
use Guava\LaravelPopulator\Concerns\Pipe\Relations\MorphRelations;
use Guava\LaravelPopulator\Exceptions\InvalidBundleException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

trait RelationsPipe
{
    use HasOneOrManyRelations;
    use BelongsRelations;
    use MorphRelations;

    /**
     * Parses the relations defined on the model and processes the supported relations.
     *
     * @param Collection $data
     * @return Collection
     * @throws InvalidBundleException
     */
    protected function relations(Collection $data): Collection
    {
        return $data
            ->mapWithKeys(function ($value, $relationName) {
                if ($this->bundle->model->isRelation($relationName)) {
                    $relation = $this->bundle->model->$relationName();

                    switch ($relation) {
                        case $relation instanceof MorphTo:
                            return $this->morphTo($relation, $value);

                        case $relation instanceof MorphOne:
                            $this->morphOne($relation, $value);
                            return [];

                        case $relation instanceof MorphMany:
                            $this->morphMany($relation, $value);
                            return [];

                        case $relation instanceof HasOne:
                            $this->hasOne($relation, $value);
                            return [];

                        case $relation instanceof HasMany:
                            $this->hasMany($relation, $value);
                            return [];

                        case $relation instanceof BelongsTo:
                            return $this->belongsTo($relation, $value);

                        case $relation instanceof BelongsToMany:
                            $this->belongsToMany($relation, $value);
                            return [];

                        default:
                            throw new InvalidBundleException("The relation type of {$relationName} is not supported yet.");
                    }
                } else {
                    return [$relationName => $value];
                }
            });
    }

}
