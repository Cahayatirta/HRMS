<?php

namespace App\Filament\Resources\DivisionResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\DivisionResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\DivisionResource\Api\Transformers\DivisionTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = DivisionResource::class;


    /**
     * Show Division
     *
     * @param Request $request
     * @return DivisionTransformer
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

        return new DivisionTransformer($query);
    }
}
