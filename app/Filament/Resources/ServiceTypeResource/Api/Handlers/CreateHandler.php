<?php
namespace App\Filament\Resources\ServiceTypeResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ServiceTypeResource;
use App\Filament\Resources\ServiceTypeResource\Api\Requests\CreateServiceTypeRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = ServiceTypeResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create ServiceType
     *
     * @param CreateServiceTypeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateServiceTypeRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}