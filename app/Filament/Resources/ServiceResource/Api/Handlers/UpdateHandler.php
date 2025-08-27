<?php
// App/Filament/Resources/ServiceResource/Api/Handlers/UpdateHandler.php
namespace App\Filament\Resources\ServiceResource\Api\Handlers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ServiceResource;
use App\Models\Service;

class UpdateHandler extends Handlers 
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = ServiceResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    public function handler(Request $request)
    {
        try {
            $id = $request->route('id');
            $service = Service::find($id);

            if (!$service) {
                return static::sendNotFoundResponse();
            }

            DB::beginTransaction();

            // 1. Update data service utama
            $serviceData = $request->only([
                'client_id', 
                'service_type_id', 
                'status', 
                'price', 
                'start_time', 
                'expired_time'
            ]);
            $service->update($serviceData);

            // 2. Handle serviceTypeData relationship
            if ($request->has('serviceTypeData')) {
                if (is_array($request->serviceTypeData)) {
                    // Delete existing serviceTypeData
                    $service->serviceTypeData()->delete();
                    
                    // Create new serviceTypeData
                    foreach ($request->serviceTypeData as $data) {
                        $service->serviceTypeData()->create([
                            'field_id' => $data['field_id'],
                            'value' => $data['value'],
                        ]);
                    }
                }
            }

            DB::commit();

            // Load relationships untuk response
            $service->load(['client', 'serviceType', 'serviceTypeData']);

            return static::sendSuccessResponse($service, "Successfully Update Resource");

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