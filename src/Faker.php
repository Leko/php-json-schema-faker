<?php
/**
 * JSON Schema faker
 *
 * @see https://github.com/Leko/php-json-schema-faker
 */

namespace JSONSchemaFaker;

use Faker\Provider\Base;
use Faker\Provider\Lorem;

/**
 * ## Example
 * ```php
 * $dummy = Faker::fake($schema);
 * ```
 */
class Faker
{
    /**
     * Create dummy data with JSON schema
     *
     * @see    http://json-schema.org
     * @param  object $schema Data structure writen in JSON Schema
     * @return mixed dummy data
     */
    public static function fake(object $schema)
    {
        $faker = new static();
        return $faker->generate($schema);
    }

    /**
     * Create dummy data with JSON schema
     *
     * @param  object $schema Data structure writen in JSON Schema
     * @return mixed dummy data
     * @throws Exception Throw when unsupported type specified
     */
    public function generate(object $schema)
    {
        $fakers = $this->getFakers();

        $type = is_array($schema->type) ? Base::randomElement($schema->type) : $schema->type;

        if (isset($schema->enum)) {
            return Base::randomElement($schema->enum);
        }

        if (!isset($fakers[$type])) {
            throw new Exception("Unsupported type: {$type}");
        }

        return $fakers[$type]($schema);
    }

    private function getFakers()
    {
        return [
            'null'    => [$this, 'fakeNull'],
            'boolean' => [$this, 'fakeBoolean'],
            'integer' => [$this, 'fakeInteger'],
            'number'  => [$this, 'fakeNumber'],
            'string'  => [$this, 'fakeString'],
            'array'   => [$this, 'fakeArray'],
            'object'  => [$this, 'fakeObject']
        ];
    }

    /**
     * Create null
     *
     * @return                                      null
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function fakeNull()
    {
        return null;
    }

    /**
     * Create dummy boolean with JSON schema
     *
     * @return                                      boolean true or false
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function fakeBoolean()
    {
        return Base::randomElement([true, false]);
    }

    /**
     * Create dummy integer with JSON schema
     *
     * @param                                       object $schema Data structure
     * @return                                      ...
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function fakeInteger(object $schema)
    {
        $minimum = getMinimum($schema);
        $maximum = getMaximum($schema);
        $multipleOf = getMultipleOf($schema);

        return (int)Base::numberBetween($minimum, $maximum) * $multipleOf;
    }

    /**
     * Create dummy floating number with JSON schema
     *
     * @param                                       object $schema Data structure
     * @return                                      ...
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function fakeNumber(object $schema)
    {
        $minimum = getMinimum($schema);
        $maximum = getMaximum($schema);
        $multipleOf = getMultipleOf($schema);

        return Base::randomFloat(null, $minimum, $maximum) * $multipleOf;
    }

    /**
     *
     *
     * @param object $schema Data structure
     * @return ...
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function fakeString(object $schema)
    {
        if (isset($schema->format)) {
            return getFormattedValue($schema);
        } elseif (isset($schema->pattern)) {
            return Lorem::regexify($schema->pattern);
        } else {
            return Lorem::text(isset($schema->maxLength) ? $schema->maxLength : 200);
        }
    }

    /**
     *
     *
     * @param object $schema Data structure
     * @return ...
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function fakeArray(object $schema)
    {
        $dummies = [];
        foreach (getItems($schema) as $subschema) {
            $dummies[] = $this->generate($subschema);
        }

        return get($schema, 'uniqueItems', false) ? array_unique($dummies) : $dummies;
    }

    /**
     * TODO: Support additionalProperties = true
     * TODO: Support patternProperties
     * TODO: Support dependencies
     *
     * @param                                       object $schema Data structure
     * @return                                      ...
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function fakeObject(object $schema)
    {
        $dummy = new \stdClass();
        foreach (getProperties($schema) as $key) {
            $subschema = $schema->properties->{$key};
            $dummy->{$key} = $this->generate($subschema);
        }

        return $dummy;
    }
}
