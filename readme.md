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
Environment::detectEnvironment([
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

## License

MIT
