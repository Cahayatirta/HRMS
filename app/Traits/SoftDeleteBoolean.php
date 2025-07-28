<?php

namespace App\Traits;

trait SoftDeleteBoolean
{
    public function softDelete()
    {
        $this->update(['isDeleted' => true]);

        // If the model has defined softCascades(), cascade delete
        if (method_exists($this, 'softCascades')) {
            foreach ($this->softCascades() as $relation) {
                foreach ($this->$relation as $child) {
                    if (method_exists($child, 'softDelete')) {
                        $child->softDelete();
                    } else {
                        $child->update(['isDeleted' => true]);
                    }
                }
            }
        }
    }

    public function restore()
    {
        $this->update(['isDeleted' => false]);
    }

    public function trashed()
    {
        return $this->isDeleted === true;
    }
}