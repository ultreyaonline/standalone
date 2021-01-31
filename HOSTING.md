# Tres Dias Community Website

## Self-hosted PHP application built on the Laravel Framework


# Requirements
To run the Ultreya application, you will need the following:
- an understanding of Laravel applications
- understanding of domains and DNS and website hosting
- hosting service - preferably a VPS (Virtual Private Server)
- email delivery service
- backups location (optional)


# Tech Stack Summary
- Server: minimum 1 GB RAM, 20 GB SSD
- Git: private repo for software version-control
- AWS S3 for backups - 4GB, used for DB backups; Optionally, profile and team-photo and banner images can be hosted on S3 as well.
- SMTP provider. Recommend Mailgun's pay-as-you-go plan. Tres Dias communities of 300-3000 members average $2-7/mo in Mailgun fees
- DNS: best if this is API-accessible via your hosting provider, to allow for LetsEncrypt certificate automation
- CI/Build Pipeline - Laravel Forge is recommended. Their "Hobby" plan would suffice.
- DevOps person who groks deploy scripts, security patching and monthly server OS patching, certbot for LetsEncrypt, and the following software components:
- Server Software will include: Ubuntu 20, Nginx, PHP 8, MySQL/MariaDB, Redis.


# Hosting
Your website needs a server to run on. There are 2 considerations: fully-managed, or self-managed.

## Fully Managed VPS
A fully-managed VPS provider, such as the services offered by Cloudways, gives an excellent balance between having the power of a server under your own control, but with a technical team taking care of regular server maintenance, and available for tech support when you run into issues.

The cost here is slightly higher than a self-managed VPS because you're paying a small fee for the Managed services.


## Self-managed VPS
If your technical team has skills related to self-managing a VPS server such as a Digital Ocean droplet or AWS instance, then you will appreciate the automation and simplicity offered by using Laravel Forge to handle server provisioning and automated deployments; this way your server techs can simply do periodic server-software updates to keep it patched against security issues, etc.

This is the optimal setup, as it automates the majority of the Laravel-related infrastructure with almost seamless convenience. The annual cost of the Forge service is not much different than the Managed-Server fees charged by Cloudways.

Alternatively, if you wish to self-manage a Digital Ocean VPS without the deployment features of Forge, you might consider the preconfigured [Laravel install](https://marketplace.digitalocean.com/apps/laravel) option in the Digital Ocean marketplace. Remember: by using this approach you will need to MANUALLY handle all deployment of code updates yourself, as well as software patching and updates.

Using AWS Lightsail is an option to consider as well, but again you will need to manually implement all the deployment features yourself, and still manage the server yourself. See later in this document for basic steps to set up Lightsail via Laravel Forge.

## Shared Hosting (Not Recommended, Nor Supported)
We do NOT recommend using a common "shared hosting" plan which runs cPanel or Plesk, as these servers often don't allow you to properly secure a Laravel application. While it "can" be done, it is a bad idea. Your tech team will want to fully understand this outdated approach before embracing it. None of the following documentation will address any of the concerns relevant to such an environment. 

---

# Server Technical Requirements
For this simple application, the smallest VPS instance size will do. 

For example, on Digital Ocean this would be the 1GB-RAM droplet (which comes with 20+ GB of disk space, which is way more than required).

---

# Email Delivery Service
Most modern VPS servers do not run an email-delivery process. This is to avoid tempting spammers to abuse the VPS services. Instead, it is strongly recommended that you utilize a 3rd-party service whose primary business is the efficient and secure delivery of email.

Community size affects the number of emails processed. During the months leading up to when you are hosting a Weekend you will use an increased amount of email correspondence. (For example, a community of 800 local members might typically use 5K emails/mo in most months, but could use 13-15K emails in a month where they host a TD Weekend.)

The Ultreya Application is ready-built for you to use Mailgun for the email-delivery service. But you can use any SMTP service that Laravel supports. 

## Mailgun
You can create a Mailgun account in just a couple minutes online at https://mailgun.com

Mailgun offers 5K emails free per month for the first 3 months.

Once you've created the account and configured the Domain and DNS, you will need your Mailgun Domain and your Mailgun Private API Key.

On your server, in the Ultreya Laravel app directory, in the `.env` file you will need to enter these details:

```text
MAIL_DRIVER=mailgun
MAILGUN_DOMAIN=your_mailgun_domain_here
MAILGUN_SECRET=key-abc12345678901234567890
```

It is wise to occasionally monitor the Mailgun account for reports of Bounced or Suppressed emails which it could not deliver, in case you have members with bad email addresses in your database.

In Mailgun you can optionally (with a paid upgrade) set up "receiving" email addresses which forward emails to certain community members. This is a convenient way to set up some vanity emails for things like Palanca and Pre-Weekend, etc.

## Alternate Email Providers

You can use any SMTP service supported by Laravel. Consult the Laravel documentation for details.

Some services which you might consider if your volume is low, include the following (but you may have to be more hands-on from a technical level):

https://kingmailer.co/

https://sendgrid.com/solutions/email-api/smtp-service/

You could also use the SMTP Relay features of GSuite or Office365 if your account has them enabled and is allowed to set application-specific passwords for a specific mailbox, in which case use those credentials in your .env file for MAIL configs.

---

# Backups

You may enable timed backups within the application: You can configure the timing of the backups in the `/App/Console/Kernal.php`, which is set to 3am by default.

If you are having the application do its own backups, you must first specify where you want those backups stored. 

Out-of-the-box it is ready to use AWS for storing your backups. (You can get your first year free with a new AWS account.)

To do this, simply set `AWS_BUCKET_BACKUPS` in your `.env` file, as well as the other 3 AWS keys for your AWS credentials.

You may want to encrypt your backups with a password before transmitting them to external storage. To do this, set `BACKUPS_PASSWORD` in your `.env` file, and remember this password someplace so that you can use that password if you need to unzip a backup in order to use it for a restore. You may want to change this password from time to time.

If you are hosting with Cloudways or another provider who handles backups for you, you can use their automated daily backup service to take a copy of your site files and database instead of, or in addition to, the above steps.


---

# Deployment
The ideal workflow for maintaining the site and pushing updates to your server involves running the system tests and notifying the server of the updated software so that it can retrieve the updates.

If you are using Laravel Forge, it provides an automated deployment process which you can use out-of-the-box. (You may want to update the deploy script to also restart the queues, etc, if you are using them.)


## Optimal Workflow
Ideally you will have your site's files in a private Github repository.

When you make a commit to Github, the Github Actions scripts will be triggered and run the test suite.

If you have Laravel Forge configured to auto-deploy on new commits to that branch, it will take care of deployment and none of the following steps are required. You could still use the Webhook URL to trigger deploys at the end of the test suite.

If you have configured a Github Secret for `DEPLOY_WEBHOOK_URL` then after the tests pass Github will use that URL to tell your server to apply the updates by running the deployment script there.

### Setting up the Deploy Secret Keys
For security, there need to be some secret keys set up so that unauthorized deploys cannot be triggered. There are 2 secret values involved: `DEPLOY_SECRET_KEY` and `DEPLOY_WEBHOOK_URL`

#### Setting `DEPLOY_SECRET_KEY`
On your server, in the `.env` file, set a value for `DEPLOY_SECRET_KEY`. A 12-20 character random string is recommended.

#### Setting up the Github Webhook to Trigger Deployment
The preferred, most secure, way to trigger deployment is by using Github Webhooks. This requires a bit of configuration:
- Login to your Github account, and open your project repository. Click on the Settings tab, and then the Webhooks tab.
- Add a Webhook, pointing to this URL: `https://your_domain.com/api/deploy`
- Content type: `application/json`
- Set the secret to match your `DEPLOY_SECRET_KEY` value from your `.env`
- Enable SSL: yes
- Events: Choose INDIVIDUAL EVENTS, and then CHECK the `Check suites` checkbox, and UNCHECK everything else including the `Pushes` checkbox.

#### Alternative to the Github Webhook: Setting `DEPLOY_WEBHOOK_URL`
Using this approach you will use Github Secrets to store the URL.
- Login to your Github account, and open your project repository. Click on the Settings tab, and then the Secrets tab.
- Add a new Secret, named `DEPLOY_WEBHOOK_URL`
- For the value, choose one of the following:

If you are using Cloudways hosting, you will give Github the following URL: `https://your_site_domain.com/deploy-webhook.php&key=foo` where `foo` matches the `DEPLOY_SECRET_KEY` in your `.env` file.

If you are using Laravel Forge, you will give Github the token URL from Forge, in the `Deployment Trigger URL` section of your Site Details page. It will look like: `https://forge.laravel.com/servers/0123123/sites/0789789/deploy/http?token=abc123456def`



---

# Initial Installation
First put your app into a Github repository.

## Laravel Forge
In the Forge portal:
- link your own SSH key to Forge for new deploys
- link your Github account within Forge under Source Control
- link DigitalOcean (or other Server Provider) account using Manage Providers
- Provision a new server with Forge
- Add a Site to that Server (delete the "default" site afterward)
- Connect the site to your Github repository and do a Deploy
- edit Environment file via Forge
  - set keys for:
      - Mailgun (outgoing mail)
      - Mailchimp (keeping mailing list in sync)
      - AWS (for daily site backups)
      - Stripe (if you're collecting payments online via the website)
  - also adjust timezone and App URL
- Tell Forge Scheduler to run a job every minute so that prayer wheel notifications get sent out properly. Something like: `php /home/forge/insert_directory_here/artisan schedule:run`
- start queue worker, so emails can send. Or switch to Laravel Horizon instead (don't start any queue workers, instead start a Daemon to run php artisan horizon, and add php artisan horizon:terminate to the deploy script). 
- You might want to update the Deploy script in Forge using steps found in the `/deploy-laravel.sh` file in your app.
- If you want the tests suite to run successfully before new deployments, use the Deployment instructions above. However, if you just want Forge to always push every new commit directly to your site without running any tests, you can enable the Auto Deploy option in Forge.

Then in your Domain Registrar's NameServer DNS settings, point the domain's A record to the server's IP address so the domain becomes live.

Then in Forge go to your Site config and add SSL to the domain via LetsEncrypt.

Be sure to do regular Linux maintenance on your server, applying regular security patches and rebooting from time to time.

Consider linking up an error-reporting service like Sentry.io or Rollbar.com or Honeybadger.com to be notified of problems users may be encountering with the site. Honeybadger includes an "uptime" monitor to let you know if the site becomes unavailable unexpectedly.


## Amazon Lightsail
Provision your server using a base Ubuntu 20.x instance, without other software installed.

IMPORTANT: Under Networking, add a Static IP. This will be the IP that's used for your server's DNS. (If you don't do this, your IP address will change repeatedly, which is unsuitable for a live website.)

Now use Laravel Forge to provision your server using "Custom VPS" option. Provide the Static IP address.

When Forge gives you the provisioning bash script to run on your server, go to Lightsail and click on the orange Terminal icon. Run `sudo -i`, press Enter, and then paste the script from Forge, press Enter, and let it run. It will take about 10 minutes.

While waiting for provisioning, in Lightsail under Networking, add a DNS Zone, linking your domain to your IP address, and do any Nameserver changes Lightsail directs you to make with your Registrar.

After provisioning, in Forge set up Site for your domain name, add the github app, do a Deploy, set Environment and Scheduler and Daemons as per the Forge steps above.

If you are continuing to use Forge with your server, set up LetsEncrypt via Forge.  If you're disconnecting Forge, then set up Certbot on the server via the command line and set a cron job for it to run weekly to check for updates to your SSL certificate.


## Cloudways
- Provision a new server
- Tell it you're installing a Laravel app
- Use github to link the repository and do a deploy. Leave the deployment path set to `public_html/`, with no subdirectory.
- Enable the API Key, so that deploys can be automated:
  - Convert/upgrade to a Paid Account if not already
  - Open the API menu (or go to `platform.cloudways.com/api`), and get the API Secret Key
  - In your `.env` file set the Cloudways settings:
  
		`CLOUDWAYS_EMAIL=` (your account email address)
		
		`CLOUDWAYS_API_KEY=` (the API Secret Key)
		
		`CLOUDWAYS_SERVER_ID=` (found in the platform URL when looking at the server details)
		
		`CLOUDWAYS_APP_ID=` (found from the URL in Application Settings screen)
		
		`CLOUDWAYS_GIT_URL=` (the SSH URL to your github repo; see Clone To Desktop dropdown)
		
		`CLOUDWAYS_GIT_BRANCH=master`
	
  - Then enable deployment as described in the Deployment section above.
- Optionally enable backups
- Tweaks; under Application Settings, the Webroot should be: `public_html/public`
- Enable a CRON job: `artisan schedule:run >> /dev/null 2>&1` (type: `PHP`, runs every minute, ie: all `*`s)
- Set an actual Domain, and enable SSL

## Other Hosting Options
There are several other VPS offerings that can work well with Laravel. Some include:
- https://moss.sh/manage-your-websites-and-servers-with-moss-free-plan/
- https://runcloud.io/docs/guide/atomic-deployment/deployment-script


---

# Misc
Some helpful devops resources can be found in the `/technical` folder.
