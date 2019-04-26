<?php

namespace JSONSchemaFaker\Test;

use JSONSchemaFaker\Faker;
use JsonSchema\Validator;

class FakerTest extends TestCase
{
    /**
     * @dataProvider getTypes
     */
    public function testFakeMustReturnValidValue($type)
    {
        $schema = $this->getFixture($type);
        $validator = new Validator();

        $actual = Faker::fake($schema);
        $validator->check($actual, $schema);

        $this->assertTrue($validator->isValid(), json_encode($validator->getErrors(), JSON_PRETTY_PRINT));
    }

    /**
     * @dataProvider getTypes
     */
    public function testFakeFromFile($type)
    {
        $schema = $this->getFile($type);
        $validator = new Validator();

        $actual = (new Faker)->generate(new \SplFileInfo($schema));
        $validator->check($actual, $schema);

        $this->assertTrue($validator->isValid(), json_encode($validator->getErrors(), JSON_PRETTY_PRINT));
    }

    public function testGenerateInvalidParameter()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new Faker)->generate(null);
    }

    public function getTypes()
    {
        return [
            ['boolean'],
            ['null'],
            ['integer'],
            ['number'],
            ['string'],
            ['array'],
            ['object'],
            ['combining'],
            ['ref_inline']
        ];
    }

    /**
     * @expectedException Exception
     */
    public function testFakeMustThrowExceptionIfInvalidType()
    {
        Faker::fake((object)['type' => 'xxxxx']);
    }
}
