<?php
namespace App\Filament\Resources\ClientResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ClientResource;
use App\Filament\Resources\ClientResource\Api\Requests\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Support\Facades\Crypt;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = ClientResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update Client
     *
     * @param UpdateClientRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateClientRequest $request, Client $client)
    {
        $validated = $request->validated();

        $clientId = $request->route('id');
        $client = Client::findOrFail($clientId);

        $clientData = $request->clientData ?? [];
        unset($validated['clientData']);

        // update data utama client
        $client->update($validated);

        if (!empty($clientData)) {
            foreach ($clientData as $data) {
                $client->clientData()->updateOrCreate(
                    [
                        'account_type' => $data['account_type'], // pakai unique key lain
                    ],
                    [
                        'account_credential' => $data['account_credential'],
                        'account_password' => Crypt::encryptString($data['account_password']),
                    ]
                );
            }
        }

        return response()->json([
            'data' => $client->load('clientData')
        ]);
    }
}

        // $id = $request->route('id');

        // $model = static::getModel()::find($id);

        // if (!$model) return static::sendNotFoundResponse();

        // $model->fill($request->all());

        // $model->save();

        // return static::sendSuccessResponse($model, "Successfully Update Resource");