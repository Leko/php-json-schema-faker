<?php

namespace JSONSchemaFaker\Test;

use JsonSchema\Validator;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class HelperTest extends TestCase
{
    public function testGetMustReturnPropertyValueIfExists()
    {
        $expected = 123;
        $obj = (object)['xxx' => $expected];
        $key = 'xxx';

        $actual = \JSONSchemaFaker\get($obj, $key);

        $this->assertSame($actual, $expected);
    }

    public function testGetMustReturnDefaultValueIfNotExists()
    {
        $expected = 123;
        $obj = (object)['xxx' => $expected];
        $key = 'aaa';

        $actual = \JSONSchemaFaker\get($obj, $key, $expected);

        $this->assertSame($actual, $expected);
    }

    public function testGetMaximumMustReturnMaximumMinusOneIfExclusiveMaximumTrue()
    {
        $maximum = 300;
        $schema = (object)['exclusiveMaximum' => true, 'maximum' => $maximum];

        $actual = \JSONSchemaFaker\getMaximum($schema);

        // -1 mean exclusive
        $this->assertSame($actual, $maximum - 1);
    }

    public function testGetMaximumMustReturnMaximumIfExclusiveMaximumFalse()
    {
        $maximum = 300;
        $schema = (object)['exclusiveMaximum' => false, 'maximum' => $maximum];

        $actual = \JSONSchemaFaker\getMaximum($schema);

        $this->assertSame($actual, $maximum);
    }

    public function testGetMaximumMustReturnMaximumIfExclusiveMaximumAbsent()
    {
        $maximum = 300;
        $schema = (object)['maximum' => $maximum];

        $actual = \JSONSchemaFaker\getMaximum($schema);

        $this->assertSame($actual, $maximum);
    }

    public function testGetMinimumMustReturnMinimumMinusOneIfExclusiveMinimumTrue()
    {
        $minimum = 300;
        $schema = (object)['exclusiveMinimum' => true, 'minimum' => $minimum];

        $actual = \JSONSchemaFaker\getMinimum($schema);

        // +1 mean exclusive
        $this->assertSame($actual, $minimum + 1);
    }

    public function testGetMinimumMustReturnMinimumIfExclusiveMinimumFalse()
    {
        $minimum = 300;
        $schema = (object)['exclusiveMinimum' => false, 'minimum' => $minimum];

        $actual = \JSONSchemaFaker\getMinimum($schema);

        $this->assertSame($actual, $minimum);
    }

    public function testGetMinimumMustReturnMinimumIfExclusiveMinimumAbsent()
    {
        $minimum = 300;
        $schema = (object)['minimum' => $minimum];

        $actual = \JSONSchemaFaker\getMinimum($schema);

        $this->assertSame($actual, $minimum);
    }

    public function testGetMultipleOfMustReturnValueIfPresent()
    {
        $expected = 7;
        $schema = (object)['multipleOf' => $expected];

        $actual = \JSONSchemaFaker\getMultipleOf($schema);

        $this->assertSame($actual, $expected);
    }

    public function testGetMultipleOfMustReturnOneIfAbsent()
    {
        $expected = 1;
        $schema = (object)[];

        $actual = \JSONSchemaFaker\getMultipleOf($schema);

        $this->assertSame($actual, $expected);
    }

    public function testGetInternetFakerInstanceMustReturnInstance()
    {
        $actual = \JSONSchemaFaker\getInternetFakerInstance();

        $this->assertTrue($actual instanceof \Faker\Provider\Internet);
    }

    /**
     * @dataProvider getFormats
     */
    public function testGetFormattedValueMustReturnValidValue($format)
    {
        $schema = (object)['type' => 'string', 'format' => $format];
        $validator = new Validator();

        $actual = \JSONSchemaFaker\getFormattedValue($schema);
        $validator->check($actual, $schema);

        $this->assertTrue($validator->isValid());
    }

    /**
     * @expectedException Exception
     */
    public function testGetFormattedValueMustThrowExceptionIfInvalidFormat()
    {
        \JSONSchemaFaker\getFormattedValue((object)['format' => 'xxxxx']);
    }

    public function testGetPropertiesMust()
    {
    }

    /**
     * @see testGetFormattedValueMustReturnValidValue
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    public function getFormats()
    {
        return [
            ['date-time'],
            ['email'],
            ['hostname'],
            ['ipv4'],
            ['ipv6'],
            ['uri'],
        ];
    }
}
