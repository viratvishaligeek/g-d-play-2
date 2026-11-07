<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Semantic alias for the existing HomePage records. The admin's HomePage
 * table and editor remain the source of truth; this class adds no table.
 */
class HomePageSection extends Model
{
    use SoftDeletes;

    protected $table = 'home_pages';
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::addGlobalScope('gali_site', function (Builder $query): void {
            $query->where($query->getModel()->qualifyColumn('site_key'), config('gali.site_key'));
        });
    }
}
