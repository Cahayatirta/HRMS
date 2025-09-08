<?php
namespace App\Filament\Resources\WorkhourPlanResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\WorkhourPlan;

/**
 * @property WorkhourPlan $resource
 */
class WorkhourPlanTransformer extends JsonResource
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
