<?php

namespace App\Types;

class HTTPMethodType {

    public const POST = "POST";

    public const GET = "GET";

    public const DELETE = "DELETE";

    public const PUT = "PUT";

    public function getConstantValue($str){
        return constant("self::$str");
    }
}