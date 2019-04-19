<?php
/**
 * JSON Schema faker
 *
 * @see https://github.com/Leko/php-json-schema-faker
 */

namespace JSONSchemaFaker;

use Faker\Provider\Base;
use Faker\Provider\Lorem;

class Faker
{
    /**
     * @var string
     */
    private $schemaDir;

    public function __construct(string $schemaDir = '')
    {
        $this->schemaDir = $schemaDir;
    }

    /**
     * Create dummy data with JSON schema
     *
     * @see    http://json-schema.org
     * @param  \stdClass $schema Data structure writen in JSON Schema
     * @return mixed dummy data
     */
    public static function fake(\stdClass $schema)
    {
        $faker = new static();
        return $faker->generate($schema);
    }

    /**
     * Create dummy data with JSON schema
     *
     * @param  \stdClass $schema Data structure writen in JSON Schema
     * @param \stdClass $parentSchema parent schema when it is subschema
     * @return mixed dummy data
     * @throws \Exception Throw when unsupported type specified
     */
    public function generate(\stdClass $schema, \stdClass $parentSchema = null)
    {
        $schema = resolveOf($schema);
        $fakers = $this->getFakers();

        if (property_exists($schema, '$ref')) {
            return $this->ref($schema, $parentSchema);
        }
        if (! isset($schema->type)) {
            throw new \Exception("No Type");
        }
        $type = is_array($schema->type) ? Base::randomElement($schema->type) : $schema->type;

        if (isset($schema->enum)) {
            return Base::randomElement($schema->enum);
        }

        if (!isset($fakers[$type])) {
            throw new \Exception("Unsupported type: {$type}");
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
     * @return null
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function fakeNull()
    {
        return null;
    }

    /**
     * Create dummy boolean with JSON schema
     *
     * @return bool true or false
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function fakeBoolean()
    {
        return Base::randomElement([true, false]);
    }

    /**
     * Create dummy integer with JSON schema
     *
     * @param  \stdClass $schema Data structure
     * @return int
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function fakeInteger(\stdClass $schema)
    {
        $minimum = getMinimum($schema);
        $maximum = getMaximum($schema);
        $multipleOf = getMultipleOf($schema);

        return (int)Base::numberBetween($minimum, $maximum) * $multipleOf;
    }

    /**
     * Create dummy floating number with JSON schema
     *
     * @param  \stdClass $schema Data structure
     * @return float
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function fakeNumber(\stdClass $schema)
    {
        $minimum = getMinimum($schema);
        $maximum = getMaximum($schema);
        $multipleOf = getMultipleOf($schema);

        return Base::randomFloat(null, $minimum, $maximum) * $multipleOf;
    }

    /**
     * @param \stdClass $schema Data structure
     * @return string
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function fakeString(\stdClass $schema)
    {
        if (isset($schema->format)) {
            return getFormattedValue($schema);
        } elseif (isset($schema->pattern)) {
            return Lorem::regexify($schema->pattern);
        } else {
            $min = get($schema, 'minLength', 1);
            $max = get($schema, 'maxLength', max(5, $min + 1));
            if ($max < 5) {
                return substr(Lorem::text(5), 0, $max);
            }
            $lorem = Lorem::text($max);

            if (mb_strlen($lorem) < $min) {
                $lorem = str_repeat($lorem, $min);
            }

            return mb_substr($lorem, 0, $max);
        }
    }

    /**
     * @param \stdClass $schema Data structure
     * @return array
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function fakeArray(\stdClass $schema)
    {
        if (!isset($schema->items)) {
            $subschemas = [$this->getRandomSchema()];
        // List
        } elseif (is_object($schema->items)) {
            $subschemas = [$schema->items];
        // Tuple
        } elseif (is_array($schema->items)) {
            $subschemas = $schema->items;
        } else {
            throw new \Exception("Invalid items");
        }

        $dummies = [];
        $itemSize = Base::numberBetween(get($schema, 'minItems', 0), get($schema, 'maxItems', count($subschemas)));
        $subschemas = array_slice($subschemas, 0, $itemSize);
        for ($i = 0; $i < $itemSize; $i++) {
            $dummies[] = $this->generate($subschemas[$i % count($subschemas)]);
        }

        return get($schema, 'uniqueItems', false) ? array_unique($dummies) : $dummies;
    }

    /**
     * @param  \stdClass $schema Data structure
     * @return \stdClass
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function fakeObject(\stdClass $schema)
    {
        $properties = get($schema, 'properties', new \stdClass());
        $propertyNames = getProperties($schema);

        $dummy = new \stdClass();
        foreach ($propertyNames as $key) {
            if (isset($properties->{$key})) {
                $subschema = $properties->{$key};
            } else {
                $subschema = getAdditionalPropertySchema($schema, $key) ?: $this->getRandomSchema();
            }

            $dummy->{$key} = $this->generate($subschema, $schema);
        }

        return $dummy;
    }

    private function getRandomSchema()
    {
        $fakerNames = array_keys($this->getFakers());

        return (object)[
            'type' => Base::randomElement($fakerNames)
        ];
    }

    private function ref(\stdClass $schema, \stdClass $parentSchema = null)
    {
        $path = (string) $schema->{'$ref'};
        if (substr($path, 0, 1) === '#') {
            return $this->embedded($parentSchema, $path);
        }
        return $this->linkedJson($parentSchema, $path);
    }

    private function embedded(\stdClass $parentSchema, string $path)
    {
        $paths = explode('/', substr($path, 2));
        $prop = $parentSchema;
        foreach ($paths as $path) {
            $prop = $prop->{$path};
        }
        $defFake = $this->generate($prop);

        return $defFake;
    }

    private function linkedJson(\stdClass $parentSchema, string $path)
    {
        $jsonPath = sprintf('%s/%s', $this->schemaDir, str_replace('./', '', $path));
        if (!file_exists($jsonPath)) {
            throw new \InvalidArgumentException($this->schemaDir);
        }
        $refJson = json_decode(file_get_contents($jsonPath));
        $fake = $this->generate($refJson, $parentSchema);

        return $fake;
    }
}
