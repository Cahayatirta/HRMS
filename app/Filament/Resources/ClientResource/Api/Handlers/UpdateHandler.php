<?php
namespace App\Filament\Resources\ClientResource\Api\Handlers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ClientResource;
use App\Models\Client;

class UpdateHandler extends Handlers 
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = ClientResource::class;

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
            $client = Client::find($id);

            if (!$client) {
                return static::sendNotFoundResponse();
            }

            DB::beginTransaction();

            // 1. Update data client utama
            $clientData = $request->only(['name', 'phone_number', 'email', 'address']);
            $client->update($clientData);

            // 2. Handle clientData relationship
            if ($request->has('clientData')) {
                if (is_array($request->clientData)) {
                    // Delete existing clientData
                    $client->clientData()->delete();
                    
                    // Create new clientData
                    foreach ($request->clientData as $data) {
                        $client->clientData()->create([
                            'account_type' => $data['account_type'],
                            'account_credential' => $data['account_credential'],
                            'account_password' => $data['account_password'], // akan di-encrypt otomatis
                        ]);
                    }
                }
            }

            DB::commit();

            // Load relationship untuk response
            $client->load('clientData');

            return static::sendSuccessResponse($client, "Successfully Update Resource");

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