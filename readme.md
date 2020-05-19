# Environment

This is a environment library for quick environment setup. [Forked from ahirarge/environment](https://github.com/ahirarge/environment)

## Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
    "require": {
        "frdl/environment": "*",
    }
}
```

## Usage

`index.php`
```php 
$environment = new Ahir\Environment\Environment;
$environment->path('/')
            ->detectEnvironment([
                'local' => ['your-machine-name']
            ]);
```

`index.php`
```php 
$environment = new Ahir\Environment\Environment;
$environment->file('/home/')
            ->detectEnvironment([
                'local' => ['your-machine-name']
            ]);
```

> You can check your machine name with `hostname` command.

`.env.local.php`
```php 
return [
        
        'secret-password' => 'secret-password-value'

    ];
```

`.env.local.json`
```json
{
    "environment": "development"
}
```

```php
$secret = getenv('secret-password');
```

## Facades 

If you wish, you can use environment library with facade connector. Please visit for guideline. [ahir/facades](https://github.com/ahirarge/facades)

## License

MIT
