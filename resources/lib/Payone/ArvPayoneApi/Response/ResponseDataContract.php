<?php

namespace Payone\ArvPayoneApi\Response;

interface ResponseDataContract
{
    /**
     * @return array
     */
    public function jsonSerialize();
}
