<?php

namespace JSONSchemaFaker\Test;

use PHPUnit\Framework\TestCase;
use JsonSchema\RefResolver;
use JsonSchema\Uri\UriRetriever;
use JsonSchema\Uri\UriResolver;

class TestCase extends TestCase
{
    protected function getFixture($name)
    {
        $refResolver = new RefResolver(new UriRetriever(), new UriResolver());
        return $refResolver->resolve('file://' . __DIR__ . "/fixture/{$name}.json");
    }

    protected function callInternalMethod($instance, $method, array $args = [])
    {
        $ref = new \ReflectionMethod(get_class($instance), $method);
        $ref->setAccessible(true);

        return $ref->invokeArgs($instance, $args);
    }
}
