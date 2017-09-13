<?php

namespace Payone\Models\Api\Clearing;

class EmptyClearing extends ClearingAbstract
{
    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [];
    }
}
