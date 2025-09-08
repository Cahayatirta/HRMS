<?php

namespace App\Filament\Resources\AttendanceResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\AttendanceResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\AttendanceResource\Api\Transformers\AttendanceTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = AttendanceResource::class;


    /**
     * Show Attendance
     *
     * @param Request $request
     * @return AttendanceTransformer
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

        return new AttendanceTransformer($query);
    }
}
