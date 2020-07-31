<?php

namespace PayoneApi\Response;

interface ResponseDataContract
{
    /**
     * @return array
     */
    public function jsonSerialize();
}
