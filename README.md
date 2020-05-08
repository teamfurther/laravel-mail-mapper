# Laravel Email to Eloquent model matcher

Mailmatch grabs incoming emails and automatically matches them to your Eloquent models.

It's great if you wish to include email in a conversation or project activity feed, in a CRM for example.

## Installation

You can install the package via composer:

```composer require teamfurther/laravel-mailmatch```

The package will automatically register itself.

This package comes with a migration to store all incoming email messages. You can publish the migration file using:

```php artisan vendor:publish --provider="Further\Mailmatch\MailmatchServiceProvider" --tag="migrations"```

Run the migrations with:

```php artisan migrate```

Next, you need to publish the configuration file:

```php artisan vendor:publish --provider="Further\Mailmatch\MailmatchServiceProvider" --tag="config"```

## Configuration

## Credits

- [Peter ILLÉS](https://github.com/ilpet)
- [Csongor UR](https://github.com/csongorur)
- [Norbert ZSOMBORI](https://github.com/zsnorbi)

## License

The MIT License (MIT). Please see [License File](https://github.com/teamfurther/laravel-mailmatch/blob/master/LICENSE.md) for more information.