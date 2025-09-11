<?php
namespace App\Filament\Resources\ServiceResource\Api\Handlers;

use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ServiceResource;
use App\Filament\Resources\ServiceResource\Api\Requests\CreateServiceRequest;
use App\Models\Service; // asumsi nama tabel untuk simpan field value

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = ServiceResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Service
     *
     * @param CreateServiceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateServiceRequest $request)
    {
        $data = $request->only([
            'client_id',
            'service_type_id',
            'status',
            'price',
            'start_time',
            'expired_time'
        ]);

        $model = new (static::getModel());
        $model->fill($data);
        $model->save();

        
        // handle repeater serviceTypeData
        if ($request->has('serviceTypeData')) {
            foreach ($request->serviceTypeData as $fieldData) {
                $model->serviceTypeData()->create([
                    'service_id' => $model->id,
                    'field_id'   => $fieldData['field_id'],
                    'value'      => $fieldData['value'],
                ]);
            }
        }

        return static::sendSuccessResponse($model->load(['client','serviceType']), "Successfully Created Service");
    }
}
