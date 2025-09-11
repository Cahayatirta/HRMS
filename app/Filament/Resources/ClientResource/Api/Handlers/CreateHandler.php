<?php
namespace App\Filament\Resources\ClientResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ClientResource;
use App\Filament\Resources\ClientResource\Api\Requests\CreateClientRequest;
use App\Models\Client;
use Illuminate\Support\Facades\Crypt;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = ClientResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Client
     *
     * @param CreateClientRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateClientRequest $request)
    {
        $validated = $request->validated();

        // Ambil data utama client
        $clientData = $request->clientData ?? [];
        unset($validated['clientData']);

        // dd($request->clientData, $clientData);

        $client = Client::create($validated);

        // Simpan clientData kalau ada
        foreach ($clientData as $data) {
            $client->clientData()->create([
                'account_type' => $data['account_type'],
                'account_credential' => $data['account_credential'],
                'account_password' => Crypt::encryptString($data['account_password']),
            ]);
        }

        return response()->json([
            'data' => $client->load('clientData')
        ]);
    }
}