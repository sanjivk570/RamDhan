<?php

declare(strict_types=1);

namespace App\Modules\Role\Models;

use App\Core\Traits\HasUuid;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * Permission model.
 *
 * Extends Spatie's Permission model to provide
 * additional application-specific attributes and
 * functionality.
 *
 * @package App\Modules\Role\Models
 * @author Sanjiv Kumar Kushwaha
 */
class Permission extends SpatiePermission
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
        'module',
        'description',
        'guard_name',
    ];
}