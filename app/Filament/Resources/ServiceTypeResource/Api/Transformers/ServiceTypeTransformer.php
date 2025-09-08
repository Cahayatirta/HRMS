<?php
namespace App\Filament\Resources\ServiceTypeResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\ServiceType;

/**
 * @property ServiceType $resource
 */
class ServiceTypeTransformer extends JsonResource
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
