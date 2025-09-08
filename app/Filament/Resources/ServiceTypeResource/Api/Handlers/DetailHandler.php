<?php

namespace App\Filament\Resources\ServiceTypeResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\ServiceTypeResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\ServiceTypeResource\Api\Transformers\ServiceTypeTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = ServiceTypeResource::class;


    /**
     * Show ServiceType
     *
     * @param Request $request
     * @return ServiceTypeTransformer
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

        return new ServiceTypeTransformer($query);
    }
}
