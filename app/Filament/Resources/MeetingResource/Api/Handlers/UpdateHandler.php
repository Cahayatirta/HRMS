<?php
namespace App\Filament\Resources\MeetingResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\MeetingResource;
use App\Filament\Resources\MeetingResource\Api\Requests\UpdateMeetingRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = MeetingResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update Meeting
     *
     * @param UpdateMeetingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateMeetingRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}