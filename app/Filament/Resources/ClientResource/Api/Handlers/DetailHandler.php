<?php

namespace App\Filament\Resources\ClientResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\ClientResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\ClientResource\Api\Transformers\ClientTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = ClientResource::class;


    /**
     * Show Client
     *
     * @param Request $request
     * @return ClientTransformer
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

        return new ClientTransformer($query);
    }
}
