<?php
namespace App\Filament\Resources\WorkhourPlanResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\WorkhourPlanResource;
use App\Filament\Resources\WorkhourPlanResource\Api\Requests\UpdateWorkhourPlanRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = WorkhourPlanResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update WorkhourPlan
     *
     * @param UpdateWorkhourPlanRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateWorkhourPlanRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}