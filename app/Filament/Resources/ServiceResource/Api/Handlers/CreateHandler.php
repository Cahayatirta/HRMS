<?php
// App/Filament/Resources/ServiceResource/Api/Handlers/CreateHandler.php
namespace App\Filament\Resources\ServiceResource\Api\Handlers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ServiceResource;
use App\Models\Service;

class CreateHandler extends Handlers 
{
    public static string | null $uri = '/';
    public static string | null $resource = ServiceResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    public function handler(Request $request)
    {
        try {
            DB::beginTransaction();

            // 1. Create Service dulu
            $serviceData = $request->only([
                'client_id', 
                'service_type_id', 
                'status', 
                'price', 
                'start_time', 
                'expired_time'
            ]);
            $serviceData['is_deleted'] = false;

            $service = Service::create($serviceData);

            // 2. Create ServiceTypeData dengan service_id yang benar
            if ($request->has('serviceTypeData') && is_array($request->serviceTypeData)) {
                foreach ($request->serviceTypeData as $data) {
                    $service->serviceTypeData()->create([
                        'field_id' => $data['field_id'],
                        'value' => $data['value'],
                    ]);
                }
            }

            DB::commit();

            // Load relationships untuk response
            $service->load(['client', 'serviceType', 'serviceTypeData']);

            return static::sendSuccessResponse($service, "Successfully Create Resource");

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}