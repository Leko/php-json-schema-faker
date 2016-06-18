<?php

namespace JSONSchemaFaker\Test;

use JsonSchema\Validator;

class FakerTest extends TestCase
{
    public function testFakeIntegerMustReturnValidValue()
    {
        $schema = $this->getFixture('integer');
        $validator = new Validator();

        $actual = $this->callInternalMethod(new Faker(), 'fake', [$schema]);

        $this->assertTrue($validator->check($actual, $schema));
    }

    public function testFakeNumberMustReturnValidValue()
    {
        $schema = $this->getFixture('number');
        $validator = new Validator();

        $actual = $this->callInternalMethod(new Faker(), 'fake', [$schema]);

        $this->assertTrue($validator->check($actual, $schema));
    }

    public function testFakeStringMustReturnValidValue()
    {
        $schema = $this->getFixture('string');
        $validator = new Validator();

        $actual = $this->callInternalMethod(new Faker(), 'fake', [$schema]);

        $this->assertTrue($validator->check($actual, $schema));
    }

    public function testFakeArrayMustReturnValidValue()
    {
        $schema = $this->getFixture('array');
        $validator = new Validator();

        $actual = $this->callInternalMethod(new Faker(), 'fake', [$schema]);

        $this->assertTrue($validator->check($actual, $schema));
    }

    /**
     * @expectedException Exception
     */
    public function testFakeMustThrowExceptionIfInvalidType()
    {
        \JSONSchemaFaker\fake((object)['type' => 'xxxxx']);
    }
}
