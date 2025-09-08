<?php
namespace App\Filament\Resources\AccessResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Access;

/**
 * @property Access $resource
 */
class AccessTransformer extends JsonResource
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
