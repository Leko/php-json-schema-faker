<?php

namespace JSONSchemaFaker\Test;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function getFixture($name)
    {
        return json_decode(file_get_contents(__DIR__ . "/fixture/{$name}.json"));
    }

    protected function callInternalMethod($instance, $method, array $args = [])
    {
        $ref = new \ReflectionMethod(get_class($instance), $method);
        $ref->setAccessible(true);

        return $ref->invokeArgs($instance, $args);
    }
}
