# Tres Dias Community Website

## Self-hosted PHP application built on the Laravel Framework


# Developer Reference

## Configuration

Be sure to configure the application using `.env` and set any special needs in the `/config/*.php` files where no `.env` settings are available. Of critical note are mysql db credentials, smtp credentials, backup-notifications-email-address, backup passwords, AWS buckets if used.

Also set up a cron job to fire the `artisan` scheduler and enable any desired jobs in `/app/Console/Kernel.php` so that things like backups and prayer-wheel notifications are automatically sent.

## Demo Data

Initial setup requires running `php artisan migrate --seed` to do db table creation and generate the required Roles and Permissions.

You may optionally also run `php artisan db:seed --class=DemoSeeder` to install some users and weekends for demo purposes.

## ERD
A database relations diagram can be found in the `/technical` folder.

--- 

## Laravel Documentation

Documentation for the Laravel framework can be found at: [Laravel Documentation](https://laravel.com/docs)

Handy training for Laravel can be obtained at [Laracasts.com](https://laracasts.com). There are several free series there explaining all the essential fundamentals for coding in and deploying Laravel applications.


## Designer Guidance

For quick-start convenience the default template is based on Bootstrap, and leverages a few jQuery plugins.

Templating Syntax: See Blade template docs: https://laravel.com/docs/views

Webpack: To compile **Front-End Assets** See Laravel-Mix docs: https://laravel.com/docs/mix

---

## Local PC Setup / Environment

It's recommended to run a local LAMP environment for development and testing.

With Laravel there are two standard options:

### Valet
For Mac users a lightweight tool called "Valet" uses Homebrew to configure local PHP/MySQL/Webserver apps and provide 90% of the same environment as the live server. In 5 minutes you can have a dev environment up and running with Valet. Instructions for setting up Valet can be found at: https://laravel.com/docs/valet

Tip: after installing Valet, run the following for a one-time setup for it to serve from your ~/Sites folder:

```
mkdir -p ~/Sites
cd ~/Sites
valet park
```

That's it. Now you can use the name of the folder plus ".test" as the URL. 

For example, if you put this project's code in your `~/Sites/tresdias` folder, simply visit `http://tresdias.test` in your browser to view it. It's that simple.


### Vagrant via Homestead

If you wish to use Vagrant, you can use Laravel Homestead for your development environment.
You can find documentation for this approach at https://laravel.com/docs/homestead


## Database Inspection

You can use tools like https://tableplus.com or http://www.sequelpro.com to inspect the local database.

## Email Testing
For testing emails during development, it’s ideal to not actually send real emails to real inboxes. 

To “trap” all the emails, [MailTrap.io](https://mailtrap.io) is a handy service. Simply configure MAIL_USERNAME and MAIL_PASSWORD in your local `.env` file with the SMTP settings from your Mailtrap account.


