<?php
namespace App\Filament\Resources\MeetingResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Meeting;

/**
 * @property Meeting $resource
 */
class MeetingTransformer extends JsonResource
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
