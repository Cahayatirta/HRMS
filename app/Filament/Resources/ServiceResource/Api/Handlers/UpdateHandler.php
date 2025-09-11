<?php
namespace App\Filament\Resources\ServiceResource\Api\Handlers;

use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ServiceResource;
use App\Filament\Resources\ServiceResource\Api\Requests\UpdateServiceRequest;
use App\Models\ServiceTypeData;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = ServiceResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    public function handler(UpdateServiceRequest $request) 
    {
        $id = $request->route('id');
        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $data = $request->only([
            'client_id',
            'service_type_id', 
            'status',
            'price',
            'start_time',
            'expired_time'
        ]);

        $model->fill($data);
        $model->save();

        if ($request->has('serviceTypeData')) {
            // Delete existing records
            $model->serviceTypeData()->delete();
            
            // Create new records
            foreach ($request->serviceTypeData as $fieldData) {
                if (isset($fieldData['field_id']) && isset($fieldData['value'])) {
                    $model->serviceTypeData()->create([
                        'field_id' => $fieldData['field_id'],
                        'value' => $fieldData['value']
                    ]);
                }
            }
        }

        return static::sendSuccessResponse(
            $model->fresh()->load(['client','serviceType','serviceTypeData']),
            "Successfully Updated Service"
        );
    }
}
