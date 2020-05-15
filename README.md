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

### Using Google as the email provider

1. Go to your Google Developers Console and enable the Gmail API.
2. Create an OAuth consent screen. Make sure the application type is "Internal".
3. Create an OAuth 2.0 client ID. Make sure the application type is "Other".
4. Add your generated client ID and client secret to your .env file. Mailmatch looks for the GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET by default. 

## Credits

- [Peter ILLÉS](https://github.com/ilpet)
- [Csongor UR](https://github.com/csongorur)
- [Norbert ZSOMBORI](https://github.com/zsnorbi)

## License

The MIT License (MIT). Please see [License File](https://github.com/teamfurther/laravel-mailmatch/blob/master/LICENSE.md) for more information.