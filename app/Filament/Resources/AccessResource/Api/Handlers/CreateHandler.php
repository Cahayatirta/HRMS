<?php
namespace App\Filament\Resources\AccessResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\AccessResource;
use App\Filament\Resources\AccessResource\Api\Requests\CreateAccessRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = AccessResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Access
     *
     * @param CreateAccessRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateAccessRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}