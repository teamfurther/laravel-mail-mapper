<?php

namespace Further\Mailmatch;

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

        $this->publishMigration();

        if ($this->app->runningInConsole()) {
            $this->commands([
                GoogleOAuthCommand::class,
            ]);
        }
    }

    /**
     * Publishes migrations.
     */
    protected function publishMigration()
    {
        if (! class_exists('CreateMailmatchAttachmentsTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_mailmatch_attachments_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_mailmatch_attachments_table.php'),
            ], 'migrations');
        }

        if (! class_exists('CreateMailmatchMessagesTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_mailmatch_messages_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_mailmatch_messages_table.php'),
            ], 'migrations');
        }

        if (! class_exists('CreateMailmatchRelationsTable')) {
            $this->publishes([
                __DIR__ . '/../database/migrations/create_mailmatch_relations_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_mailmatch_relations_table.php'),
            ], 'migrations');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mailmatch.php', 'mailmatch');
    }
}
