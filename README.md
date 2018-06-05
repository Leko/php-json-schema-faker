
# PHP JSON Schema Faker
[![CircleCI](https://circleci.com/gh/Leko/php-json-schema-faker.svg?style=svg)](https://circleci.com/gh/Leko/php-json-schema-faker)
[![codecov](https://codecov.io/gh/Leko/php-json-schema-faker/branch/master/graph/badge.svg)](https://codecov.io/gh/Leko/php-json-schema-faker)

Create dummy data with JSON schema.  
Inspire from [json-schema-faker](https://github.com/json-schema-faker/json-schema-faker)

## Getting started

```bash
composer require leko/json-schema-faker
```

```php
<?php

require_once 'PATH/TO/vendor/autoload.php';

use JSONSchemaFaker\Faker;

$schema = json_decode(file_get_contents('./schema.json'));
$dummy = Faker::fake($schema);
```

## Contribution

* Fork this repo
* Write your code
* Create PR to `master` branch

## License

MIT
