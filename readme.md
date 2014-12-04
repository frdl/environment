# Environment

This is a environment library for quick environment setup.

## Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
    "require": {
        "ahir/environment": "1.*",
    }
}
```

## Usage

`index.php`
```php 
$environment = new Ahir\Environment\Environment;
$environment->detectEnvironment([
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

```php
$secret = getenv('secret-password');
```

## Facades 

If you wish, you can use environment library with facade connector. Please visit for guideline. [ahir/facades](https://github.com/ahirarge/facades)

## License

MIT
