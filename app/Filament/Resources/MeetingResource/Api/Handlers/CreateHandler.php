<?php
namespace App\Filament\Resources\MeetingResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\MeetingResource;
use App\Filament\Resources\MeetingResource\Api\Requests\CreateMeetingRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = MeetingResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Meeting
     *
     * @param CreateMeetingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateMeetingRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}