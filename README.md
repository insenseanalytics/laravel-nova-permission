# A Laravel Nova tool for the Spatie Permission package

 [![License](https://poser.pugx.org/insenseanalytics/laravel-nova-permission/license)](https://packagist.org/packages/insenseanalytics/laravel-nova-permission)
 [![Latest Stable Version](https://poser.pugx.org/insenseanalytics/laravel-nova-permission/v/stable)](https://packagist.org/packages/insenseanalytics/laravel-nova-permission)
 [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/insenseanalytics/laravel-nova-permission/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/insenseanalytics/laravel-nova-permission/?branch=master)
 [![Total Downloads](https://poser.pugx.org/insenseanalytics/laravel-nova-permission/downloads)](https://packagist.org/packages/insenseanalytics/laravel-nova-permission)

This [Nova](https://nova.laravel.com) tool lets you:
- manage roles and permissions on the Nova dashboard
- use permissions based authorization for Nova resources

## Screenshots
<img alt="screenshot of the backup tool" src="https://insenseanalytics.github.io/public-assets/laravel-nova-permission/nova-permission-screenshot.png" />

## Requirements & Dependencies
There are no PHP dependencies except the [Laravel Nova](https://nova.laravel.com) package and the [Spatie Permission](https://github.com/spatie/laravel-permission) package.

## Installation
You can install this tool into a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require insenseanalytics/laravel-nova-permission
```

Next, if you do not have package discovery enabled, you need to register the provider in the `config/app.php` file.
```php
'providers' => [
    ...,
    Insenseanalytics\LaravelNovaPermission\NovaPermissionServiceProvider::class,
]
```

Next, you must register the tool with Nova. This is typically done in the `tools` method of the `NovaServiceProvider`.

```php
// in app/Providers/NovaServiceProvider.php

public function tools()
{
    return [
        // ...
        \Insenseanalytics\LaravelNovaPermission\LaravelNovaPermission::make(),
    ];
}
```

Next, add `MorphToMany` fields to you `app/Nova/User` resource:

```php
use Laravel\Nova\Fields\MorphToMany;

public function fields(Request $request)
{
    return [
        // ...
        MorphToMany::make('Roles', 'roles', \Insenseanalytics\LaravelNovaPermission\Role::class),
        MorphToMany::make('Permissions', 'permissions', \Insenseanalytics\LaravelNovaPermission\Permission::class),
    ];
}
```

Finally, add the `ForgetCachedPermissions` class to your `config/nova.php` middleware like so:

```php
// in config/nova.php
'middleware' => [
	'web',
	Authenticate::class,
	DispatchServingNovaEvent::class,
	BootTools::class,
	Authorize::class,
	\Insenseanalytics\LaravelNovaPermission\ForgetCachedPermissions::class,
],
```

## Localization

You can use the artisan command line tool to publish localization files:

```php
php artisan vendor:publish --provider="Insenseanalytics\LaravelNovaPermission\NovaPermissionServiceProvider"
```

## Using Custom Role/Permission Resource Classes

If you want to use custom resource classes you can define them when you register a tool:

```php
// in app/Providers/NovaServiceProvider.php

public function tools()
{
    return [
        // ...
        \Insenseanalytics\LaravelNovaPermission\LaravelNovaPermission::make()
            ->roleResource(CustomRole::class)
            ->permissionResource(CustomPermission::class),
    ];
}
```

## Permissions Based Authorization for Nova Resources
By default, Laravel Nova uses Policy based authorization for Nova resources. If you are using the Spatie Permission library, it is very likely that you would want to swap this out to permission based authorization without the need to define Authorization policies.

To do so, you can use the `PermissionsBasedAuthTrait` and define a `permissionsForAbilities` static array property in your Nova resource class like so:

```php
// in app/Nova/YourNovaResource.php

class YourNovaResource extends Resource
{
    use \Insenseanalytics\LaravelNovaPermission\PermissionsBasedAuthTrait;

    public static $permissionsForAbilities = [
      'all' => 'manage products',
    ];
}
```

The example above means that all actions on this resource can be performed by users who have the "manage products" permission. You can also define separate permissions for each action like so:

```php
    public static $permissionsForAbilities = [
      'viewAny' => 'view products',
      'view' => 'view products',
      'create' => 'create products',
      'update' => 'update products',
      'delete' => 'delete products',
      'restore' => 'restore products',
      'forceDelete' => 'forceDelete products',
      'addAttribute' => 'add product attributes',
      'attachAttribute' => 'attach product attributes',
      'detachAttribute' => 'detach product attributes',
    ];
```

### Relationships 
To allow your users to specify a relationship on your model, you will need to add another permission on the Model. 
For example, if your `Product` belongs to `User`, add the following permission on `app/Nova/User.php`. : 

```php
    public static $permissionsForAbilities = [
      'addProduct' => 'add user on products'
    ];
```

## Contributing

Contributions are welcome and will be fully credited as long as you use PSR-2, explain the issue/feature that you want to solve/add and back your code up with tests. Happy coding!

## License

The MIT License (MIT). Please see [License File](LICENSE.txt) for more information.
