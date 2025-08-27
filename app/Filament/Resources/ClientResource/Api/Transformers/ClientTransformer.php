<?php
namespace App\Filament\Resources\ClientResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Client;

/**
 * @property Client $resource
 */
class ClientTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
