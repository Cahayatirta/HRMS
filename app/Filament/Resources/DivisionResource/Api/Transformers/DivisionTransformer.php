<?php
namespace App\Filament\Resources\DivisionResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Division;

/**
 * @property Division $resource
 */
class DivisionTransformer extends JsonResource
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
