<?php

namespace JSONSchemaFaker\Test;

use JSONSchemaFaker\Faker;
use JsonSchema\Validator;

class FakerTest extends TestCase
{
    public function testFakeIntegerMustReturnValidValue()
    {
        $schema = $this->getFixture('integer');
        $validator = new Validator();

        $actual = Faker::fake($schema);
        $validator->check($actual, $schema);

        $this->assertTrue($validator->isValid());
    }

    public function testFakeNumberMustReturnValidValue()
    {
        $schema = $this->getFixture('number');
        $validator = new Validator();

        $actual = Faker::fake($schema);
        $validator->check($actual, $schema);

        $this->assertTrue($validator->isValid());
    }

    public function testFakeStringMustReturnValidValue()
    {
        $schema = $this->getFixture('string');
        $validator = new Validator();

        $actual = Faker::fake($schema);
        $validator->check($actual, $schema);

        $this->assertTrue($validator->isValid());
    }

    public function testFakeArrayMustReturnValidValue()
    {
        $schema = $this->getFixture('array');
        $validator = new Validator();

        $actual = Faker::fake($schema);
        $validator->check($actual, $schema);

        $this->assertTrue($validator->isValid());
    }

    /**
     * @expectedException Exception
     */
    public function testFakeMustThrowExceptionIfInvalidType()
    {
        Faker::fake((object)['type' => 'xxxxx']);
    }
}
