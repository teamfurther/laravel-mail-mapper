<?php

namespace Further\Mailmatch;

use Further\Mailmatch\Console\Commands\SyncCommand;
use Further\Mailmatch\Facades\Mailmatch;
use Illuminate\Support\ServiceProvider;
use Further\Mailmatch\Console\Commands\GoogleOAuthCommand;

class MailmatchServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/mailmatch.php' => config_path('mailmatch.php'),
        ], 'config');

        $this->publishMigrations();


        if ($this->app->runningInConsole()) {
            $this->commands([
                GoogleOAuthCommand::class,
                SyncCommand::class,
            ]);
        }

        $this->registerDriver();
    }

    /**
     * Publishes migrations.
     */
    protected function publishMigrations()
    {
        if (! class_exists('CreateMailmatchMessagesTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_mailmatch_messages_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_mailmatch_messages_table.php'),
            ], 'migrations');
        }

        if (! class_exists('CreateMailmatchMessageAttachmentsTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_mailmatch_message_attachments_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', (time() + 1)) . '_create_mailmatch_message_attachments_table.php'),
            ], 'migrations');
        }

        if (! class_exists('CreateMailmatchMessageRecipientsTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_mailmatch_message_recipients_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', (time() + 1)) . '_create_mailmatch_message_recipients_table.php'),
            ], 'migrations');
        }

        if (! class_exists('CreateMailmatchMessageRelationsTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_mailmatch_message_relations_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', (time() + 1)) . '_create_mailmatch_message_relations_table.php'),
            ], 'migrations');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mailmatch.php', 'mailmatch');

        $this->app->singleton('mailmatch', function () {
            return new MailmatchManager($this->app);
        });
    }

    /**
     * Registers the correct driver.
     */
    protected function registerDriver()
    {
        Mailmatch::register();
    }
}
