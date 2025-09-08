<?php

namespace App\Filament\Resources\MeetingResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\MeetingResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\MeetingResource\Api\Transformers\MeetingTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = MeetingResource::class;


    /**
     * Show Meeting
     *
     * @param Request $request
     * @return MeetingTransformer
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

        return new MeetingTransformer($query);
    }
}
