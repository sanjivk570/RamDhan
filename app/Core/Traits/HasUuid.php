<?php

declare(strict_types=1);

namespace App\Core\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

//Todo - need to implement with user uuid
trait HasUuid
{
    protected static function bootHasUuid(): void
    {
        // static::creating(function (Model $model): void {
        //     if (empty($model->uuid)) {
        //         $model->uuid = (string) Str::uuid();
        //     }
        // });
    }
}
