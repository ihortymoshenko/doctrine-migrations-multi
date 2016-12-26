# doctrine-migrations-multi #

## Overview ##

This repository provides a simple script that adds the ability to use
[Doctrine Migrations][1] with more than one (multiple) connection (database).

## Installation ##

### 1. Using Composer (recommended) ###

To install `doctrine-migrations-multi` with [Composer][2] just add the
following to your `composer.json` file:

```json
{
    "require": {
        "imt/doctrine-migrations-multi": "dev-master"
    }
}
```

Then, you can install the new dependencies by running Composer's update command
from the directory where your `composer.json` file is located:

```sh
$ php composer.phar update imt/doctrine-migrations-multi
```

Now, Composer will automatically download all required files, and install them
for you.

## Usage ##

To start using `doctrine-migrations-multi` create the
`migrations-db-multi.php` file that returns an array of connections:

migrations-db-multi-example.php
```php
<?php

return [
    'foo' => [
        'dbname'   => 'foo',
        'user'     => 'foo',
        'password' => 'foo',
        'host'     => 'localhost',
        'driver'   => 'pdo_mysql'
    ],
    'bar' => [
        'dbname'   => 'bar',
        'user'     => 'bar',
        'password' => 'bar',
        'host'     => 'localhost',
        'driver'   => 'pdo_mysql'
    ],
];
```

Then, run `doctrine-migrations-multi` with needed command (for instance with `migrations:migrate`):

```sh
php vendor/bin/doctrine-migrations-multi \
    migrations:migrate \
    --configuration=<path-to>/migrations.yml \
    --connection=foo \
    --db-configuration-multi=<path-to>/migrations-db-multi.php
```

## Testing

```sh
$ make test
```

## Contributing ##

Please see [CONTRIBUTING][3] for details.

## Credits

- [Igor Timoshenko][4]
- [All Contributors][5]

## License ##

This library is released under the MIT license. See the complete license in the
`LICENSE` file that is distributed with this source code.

[1]: https://github.com/doctrine/migrations
[2]: http://getcomposer.org
[3]: https://github.com/IgorTimoshenko/doctrine-migrations-multi/blob/master/CONTRIBUTING.md
[4]: https://github.com/IgorTimoshenko
[5]: https://github.com/IgorTimoshenko/doctrine-migrations-multi/graphs/contributors
