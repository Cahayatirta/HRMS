<?php
namespace App\Filament\Resources\ServiceTypeResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ServiceTypeResource;
use App\Filament\Resources\ServiceTypeResource\Api\Requests\UpdateServiceTypeRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = ServiceTypeResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update ServiceType
     *
     * @param UpdateServiceTypeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateServiceTypeRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}