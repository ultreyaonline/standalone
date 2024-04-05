# Tres Dias Community Website

## Self-hosted PHP application built on the Laravel Framework


# Developer Reference

## Configuration

Be sure to configure the application using `.env` and set any special needs in the `/config/*.php` files where no `.env` settings are available. Of critical note are mysql db credentials, smtp credentials, backup-notifications-email-address, backup passwords, AWS buckets if used.

For a production server, be sure to set up a cron job to fire the `artisan schedule:run` and enable any desired jobs in `/app/Console/Kernel.php` so that things like backups and prayer-wheel notifications are automatically sent.

## Demo Data

Initial setup requires running `php artisan migrate --seed` to do db table creation and generate the required Roles and Permissions.

You may optionally also run `php artisan db:seed --class=DemoSeeder` to install some users and weekends for demo purposes.

## ERD
A database relations diagram can be found in the `/technical` folder.

--- 

## Laravel Documentation

Documentation for the Laravel framework can be found at: [Laravel Documentation](https://laravel.com/docs)

Handy training for Laravel can be obtained at [Laracasts.com](https://laracasts.com), where you will find several free series explaining all the essential fundamentals for coding in and deploying Laravel applications.


## Designer Guidance

For quick-start convenience the default template is based on Bootstrap, and leverages a few jQuery plugins.

Templating Syntax: See Blade template docs: https://laravel.com/docs/views

Webpack: To compile **Front-End Assets** See Laravel-Mix docs: https://laravel.com/docs/mix

---

## Local PC Setup / Environment

You will need a LAMP environment for development and testing.

With Laravel there are three standard options:

### Sail (using Docker)

For Windows/Mac/Linux, you can use Sail to quickly set up a dev environment and work with the application. You don't need to be a Docker expert to use Sail! 

You can find full documentation on using Sail at: https://laravel.com/docs/8.x/sail

Sail is probably the fastest/easiest setup option.

### Herd

For Mac and Windows, a super-straightforward tool [Laravel Herd](https://herd.laravel.com) handles all your local development requirements. You can be up and running in under 5 minutes.

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

You could even run `cd ~/Sites/tresdias && valet secure` to make it work with https at `https://tresdias.test`


### Homestead (using Vagrant)

If you wish to use a Vagrant virtual machine, you can use [Laravel Homestead](https://laravel.com/docs/homestead) for your development environment.


## Database Inspection

You can use tools like https://tableplus.com , https://dbngin.com or http://www.sequelpro.com to inspect the local database.


## Email Testing
For testing emails during development, it’s ideal to not actually send real emails to real inboxes. 

To “trap” all the emails, [MailHog](https://github.com/mailhog/MailHog), [MailTrap.io](https://mailtrap.io) and [HELO](https://usehelo.com/) are handy options.

MailHog is installed and available within Sail already. All emails sent by the dockerized application will be sent to Mailhog.

For Mailtrap, once you've created your free account, simply configure MAIL_USERNAME and MAIL_PASSWORD in your local `.env` file with the SMTP settings from your Mailtrap account.
