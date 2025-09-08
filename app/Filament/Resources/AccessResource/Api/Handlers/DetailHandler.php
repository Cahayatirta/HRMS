<?php

namespace App\Filament\Resources\AccessResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\AccessResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\AccessResource\Api\Transformers\AccessTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = AccessResource::class;


    /**
     * Show Access
     *
     * @param Request $request
     * @return AccessTransformer
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

        return new AccessTransformer($query);
    }
}
