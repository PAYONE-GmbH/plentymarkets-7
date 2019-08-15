<?php

namespace PayoneApi\Request;

class SerializerFactory
{
    public static function createArraySerializer()
    {
        return new ArraySerializer();
    }
}
