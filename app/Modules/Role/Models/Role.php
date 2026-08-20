<?php

declare(strict_types=1);

namespace App\Modules\Role\Models;

use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Role model.
 *
 * Extends Spatie's Role model to provide
 * additional application-specific attributes and
 * functionality.
 *
 * @package App\Modules\Role\Models
 * @author Sanjiv Kumar Kushwaha
 */
class Role extends SpatieRole
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'display_name',
        'description',
        'guard_name',
        'is_system',
    ];
}