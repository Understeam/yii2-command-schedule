# Yii2 Command Scheduler

This command is alternative for `cron` and any other cron scheduling components.
It allows to schedule any task dynamically from code:

```php
Yii::$app->schedule->add(
    'my-unique-command-key',    // Unique task key. If task already exists it will be replaced
    $command,                   // Command. May be literally anything. See Executor section
    '0 0 * * *',                // Cron expression
    false                       // Repeat after success execution? Default - true
);
```

This is lightweight operation which allows to postpone any heavy tasks into background.

This tool is `cron` based, so, you need to run a scheduler script every minute.
Example entry of `crontab` file:

```
* * * * * /usr/bin/php /var/www/my-application/yii scheduler/cron --interactive=false
```

## Executor

> TODO

## Command

> TODO

Sorry about docs, they are not ready yet. You can inspect tests and code to see how it works, it's quite simple.
