<?php
/**
 * JSON Schema faker
 *
 * @see https://github.com/Leko/php-json-schema-faker
 */

namespace JSONSchemaFaker;

use Faker\Factory;
use Faker\Provider\Base;
use Faker\Provider\DateTime;
use Faker\Provider\Internet;

/**
 * Get value without E_NOTICE
 *
 * @param  \stdClass $obj     Target
 * @param  string $prop    Property name
 * @param  mixed  $default Value if $obj->{$prop} does not exist
 * @return mixed property value or default value
 */
function get($obj, $prop, $default = null)
{
    return isset($obj->{$prop}) ? $obj->{$prop} : $default;
}

/**
 * Get maximum number
 *
 * @param  \stdClass $schema Data structure
 * @return int maximum number
 */
function getMaximum($schema)
{
    $offset = get($schema, 'exclusiveMaximum', false) ? 1 : 0;
    return (int)(get($schema, 'maximum', mt_getrandmax()) - $offset);
}

/**
 *
 *
 * @param \stdClass $schema Data structure
 * @return ...
 */
function getMinimum($schema)
{
    $offset = get($schema, 'exclusiveMinimum', false) ? 1 : 0;
    return (int)(get($schema, 'minimum', -mt_getrandmax()) + $offset);
}

/**
 *
 *
 * @param \stdClass $schema Data structure
 * @return ...
 */
function getMultipleOf($schema)
{
    return get($schema, 'multipleOf', 1);
}

function getInternetFakerInstance()
{
    return new Internet(Factory::create());
}

/**
 *
 *
 * @param \stdClass $schema Data structure
 * @return ...
 */
function getFormattedValue($schema)
{
    switch ($schema->format) {
        // Date representation, as defined by RFC 3339, section 5.6.
        case 'date-time':
            return DateTime::dateTime()->format(DATE_RFC3339);
        // Internet email address, see RFC 5322, section 3.4.1.
        case 'email':
            return getInternetFakerInstance()->safeEmail();
        // Internet host name, see RFC 1034, section 3.1.
        case 'hostname':
            return getInternetFakerInstance()->domainName();
        // IPv4 address, according to dotted-quad ABNF syntax as defined in RFC 2673, section 3.2.
        case 'ipv4':
            return getInternetFakerInstance()->ipv4();
        // IPv6 address, as defined in RFC 2373, section 2.2.
        case 'ipv6':
            return getInternetFakerInstance()->ipv6();
        // A universal resource identifier (URI), according to RFC3986.
        case 'uri':
            return getInternetFakerInstance()->url();
        default:
            throw new \Exception("Unsupported type: {$schema->format}");
    }
}

/**
 *
 * @return string[] Property names
 */
function getProperties(\stdClass $schema)
{
    $requiredKeys = get($schema, 'required', []);
    $optionalKeys = array_keys((array)$schema->properties);
    $minProperties = get($schema, 'minProperties', 0);
    $maxProperties = get($schema, 'maxProperties', count($optionalKeys) - count($requiredKeys));
    $additionalKeys = Base::randomElements($optionalKeys, Base::numberBetween($minProperties, $maxProperties));

    return array_unique(array_merge($requiredKeys, $additionalKeys));
}
