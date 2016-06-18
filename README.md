
# PHP JSON Schema Faker

Create dummy data with JSON schema.  
Inspire from [json-schema-faker](https://github.com/json-schema-faker/json-schema-faker)

## Getting started

```
composer require leko/json-schema-faker
```

If schema has references(`$ref`), please use with []().

```
<?php

require_once 'PATH/TO/vendor/autoload.php';

use JsonSchema\RefResolver;
use JsonSchema\Uri\UriRetriever;
use JsonSchema\Uri\UriResolver;

$refResolver = new RefResolver(new UriRetriever(), new UriResolver());
$schema = $refResolver->resolve('file://' . realpath('schema.json'));

$dummy = JSONSchemaFaker\fake($schema);
```

## Contribution

* Fork this repo
* Write your code
* Create PR to `master` branch

## License

MIT
