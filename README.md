# Axis4

A WordPress must-use (MU) plugin for developing highly customized, modern PHP based websites.

## Installation
Download this plugin and place under your WordPress `mu-plugins` directory.

Simply symlink axis4.php or create MU plugin loader like:
```php
<?php
if ( file_exists(__DIR__ . '/axis4/axis4.php' ) ) {
    require_once __DIR__ . '/axis4/axis4.php';
}
```

Do not forget to create autoloder files. Run:
```
composer dump-autoload    # OR
composer dump-autoload -a # Generates optimizeed autloading files.
```

## Getting Started
Theme:
```php
<?php

define('THEME_VERSION', '1.0.0');
define('THEME_PRIORITY', 250);

axis()
    ->makeTheme('theme-thing')
    ->setVersion(THEME_THING_VERSION)
    ->setDefaultPriority(THEME_PRIORITY)
    ->setRootModules(
        [
            'foo' => Foo::class,
            'bar' => Bar::class,
        ]
    )
    ->standby()
;
```
