<?php
namespace App\Filament\Resources\ClientResource\Api\Handlers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ClientResource;
use App\Models\Client;

class CreateHandler extends Handlers 
{
    public static string | null $uri = '/';
    public static string | null $resource = ClientResource::class;
    
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
            
            // 1. Create Client dulu
            $clientData = $request->only(['name', 'phone_number', 'email', 'address']);
            $clientData['is_deleted'] = false;
            
            $client = Client::create($clientData);
            
            // 2. Create ClientData dengan client_id yang benar
            if ($request->has('clientData') && is_array($request->clientData)) {
                foreach ($request->clientData as $data) {
                    $client->clientData()->create([
                        'account_type' => $data['account_type'],
                        'account_credential' => $data['account_credential'], 
                        'account_password' => $data['account_password'], // akan di-encrypt otomatis di model
                    ]);
                }
            }
            
            DB::commit();
            
            // Load relationship untuk response
            $client->load('clientData');
            
            return static::sendSuccessResponse($client, "Successfully Create Resource");
            
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