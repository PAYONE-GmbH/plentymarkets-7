<?php

namespace Payone\ArvPayoneApi\Request;

class SerializerFactory
{
    public static function createArraySerializer()
    {
        return new ArraySerializer();
    }
}
