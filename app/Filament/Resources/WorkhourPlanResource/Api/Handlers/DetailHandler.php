<?php

namespace App\Filament\Resources\WorkhourPlanResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\WorkhourPlanResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\WorkhourPlanResource\Api\Transformers\WorkhourPlanTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = WorkhourPlanResource::class;


    /**
     * Show WorkhourPlan
     *
     * @param Request $request
     * @return WorkhourPlanTransformer
     */
    public function handler(Request $request)
    {
        $id = $request->route('id');
        
        $query = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $query->where(static::getKeyName(), $id)
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        return new WorkhourPlanTransformer($query);
    }
}
