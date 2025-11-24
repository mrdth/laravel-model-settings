# Easy to use way to add settings to any Eloquent model

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mrdth/laravel-model-settings.svg?style=flat-square)](https://packagist.org/packages/mrdth/laravel-model-settings)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mrdth/laravel-model-settings/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mrdth/laravel-model-settings/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mrdth/laravel-model-settings/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/mrdth/laravel-model-settings/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mrdth/laravel-model-settings.svg?style=flat-square)](https://packagist.org/packages/mrdth/laravel-model-settings)

This package utilizes Eloquents JSON casting to provide a simple way to add settings to any Eloquent model.

## Installation

You can install the package via composer:

```bash
composer require mrdth/laravel-model-settings
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-model-settings-config"
```

This is the contents of the published config file:

```php
return [
    'column' => env('MRDTH_MODEL_SETTINGS_COLUMN_NAME', 'settings'),
];
```

## Usage

### Migrations

First add the settings column to your model's migration.

```php
...
    $table->json('settings')->nullable();
...
```

For existing models you can create a migration using our artisan command.

```bash
php artisan make::msm {model}
```

To change the column used for settings you can update the `MRDTH_MODEL_SETTINGS_COLUMN_NAME` in your `.env` file.

### Models

Next add the `HasSettings` trait to any Eloquent model you want to have settings.

```php
...
use Illuminate\Notifications\Notifiable;
use Mrdth\LaravelModelSettings\Concerns\HasSettings;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasSettings;
...
```

### Using the model settings

Once the trait is added you can interact with the settings using methodsL

```php
$user = User::find(1);
$user->hasSetting('use custom avatar'); // false

$user->setSetting('use custom avatar', true);
$user->hasSetting('use custom avatar'); // true
$user->getSetting('use custom avatar'); // true

$user->updateSetting('use custom avatar', false);
$user->getSetting('use custom avatar'); // false
$user->getSetting('non-existent setting'); // null
$user->getSetting('non-existent setting', 'default value'); // 'default value'

$user->getSettings(); // ['use custom avatar' => false]

$user->deleteSetting('use custom avatar');
$user->hasSetting('use custom avatar'); // false

$user->deleteSettings();
```

### Query Scopes

You can also query models based on their settings.

```php
$users = User::whereHasSetting('use custom avatar')->get(); // Returns all users with the setting 'use custom avatar'
$users = User::whereHasSetting('use custom avatar', true)->get(); // Returns all users with the setting 'use custom avatar' set to true
```

## Testing

```bash
composer tests
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [mrdth](https://github.com/mrdth)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
