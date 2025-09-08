<?php
namespace App\Filament\Resources\AttendanceResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Attendance;

/**
 * @property Attendance $resource
 */
class AttendanceTransformer extends JsonResource
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
