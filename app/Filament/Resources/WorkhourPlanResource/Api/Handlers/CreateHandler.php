<?php
namespace App\Filament\Resources\WorkhourPlanResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\WorkhourPlanResource;
use App\Filament\Resources\WorkhourPlanResource\Api\Requests\CreateWorkhourPlanRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = WorkhourPlanResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create WorkhourPlan
     *
     * @param CreateWorkhourPlanRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateWorkhourPlanRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}