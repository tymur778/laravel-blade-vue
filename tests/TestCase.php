<?php

namespace Tests;

use App\Http\Controllers\OpenAIController;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use ReflectionClass;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function callPrivate(Object $object, string $method, array $params = []): mixed
    {
        $reflection = new ReflectionClass($object);
        $reflectedMethod = $reflection->getMethod($method);
        $reflectedMethod->setAccessible(true);
        $res = $reflectedMethod->invokeArgs($object, $params);

        return $res;
    }
}
