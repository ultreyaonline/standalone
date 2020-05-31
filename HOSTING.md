# Tres Dias Community Website

## Self-hosted PHP application built on the Laravel Framework


# Requirements
To run the Ultreya application, you will need the following:
- an understanding of Laravel applications
- understanding of domains and DNS and website hosting
- hosting service - preferably a VPS (Virtual Private Server)
- email delivery service
- backups location (optional)


# Hosting
Your website needs a server to run on. There are 2 considerations: fully-managed, or self-managed.

## Fully Managed VPS
A fully-managed VPS provider, such as the services offered by Cloudways, gives an excellent balance between having the power of a server under your own control, but with a technical team taking care of regular server maintenance, and available for tech support when you run into issues.

The cost here is slightly higher than a self-managed VPS because you're paying a small fee for the Managed services.

## Self-managed VPS
If your technical team has skills related to self-managing a VPS server such as a Digital Ocean droplet or AWS instance, then you will appreciate the automation and simplicity offered by using Laravel Forge to handle server provisioning and automated deployments; this way your server techs can simply do periodic server-software updates to keep it patched against security issues, etc.

This is the optimal setup, as it automates the majority of the Laravel-related infrastructure with almost seamless convenience. The annual cost of the Forge service is not much different than the Managed-Server fees charged by Cloudways.

## Shared Server (Not Recommended)
We do NOT recommend using a common "shared hosting" plan which runs cPanel or Plesk, as these servers often don't allow you to properly secure a Laravel application. It "can" be done, but your tech team will want to fully understand this before using it. None of the following documentation will address any of the concerns relevant to such an environment. 

---

# Server Technical Requirements
For this simple application, the smallest VPS instance size will do. 

For example, on Digital Ocean this would be the 1GB-RAM droplet (which comes with 20+ GB of disk space, which is way more than required).

---

# Email Delivery Service
Most modern VPS servers do not run an email-delivery process. This is to avoid tempting spammers to abuse the VPS services. Instead, it is strongly recommended that you utilize a 3rd-party service whose primary business is the efficient and secure delivery of email.

Community size affects the number of emails processed. During the months leading up to when you are hosting a Weekend you will use an increased amount of email correspondence. (For example, a community of 800 local members might typically use 5K emails/mo in most months, but could use 13-15K emails in a month where they host a TD Weekend.)

The Ultreya Application is ready-built for you to use Mailgun for the email-delivery service. But, you can of course use any SMTP service that Laravel supports. 

## Mailgun
You can create a Mailgun account in just a couple minutes online at mailgun.com

Mailgun offers 5K emails free per month for the first 3 months.

Once you've created the account and configured the Domain and DNS, you will need your Mailgun Domain and your Mailgun Private API Key.

On your server, in the Ultreya Laravel app directory, in the `.env` file you will need to enter these details:

```text
MAIL_DRIVER=mailgun
MAILGUN_DOMAIN=your_mailgun_domain_here
MAILGUN_SECRET=key-abc12345678901234567890
```

It is wise to occasionally monitor the account for reports of Bounced or Suppressed emails which it could not deliver, in case you have members with bad email addresses in your database.

In Mailgun you can optionally set up "receiving" email addresses which forward emails to certain community members. This is a convenient inexpensive way to set up some vanity emails for things like Palanca and Pre-Weekend, etc.

## Alternate Email Providers

You can use any SMTP service supported by Laravel. Consult the Laravel documentation for details.

---

# Backups
If you are hosting with Cloudways, you can use their automated daily backup service to take a copy of your site files and database.

You may optionally wish to enable timed backups within the application: You can configure the timing of the backups in the `/App/Console/Kernal.php`, which is set to 3am by default.

If you are having the application do its own backups, you must first specify where you want those backups stored. Out-of-the-box it is ready to use AWS for storing your backups. To do this, simply set `AWS_BUCKET_BACKUPS` in your `.env` file.

You may want to encrypt your backups with a password before transmitting them to external storage. To do this, set `BACKUPS_PASSWORD` in your `.env` file, and remember this password someplace so that you can use that password if you need to unzip a backup in order to use it for a restore. You may want to change this password from time to time.


---

# Deployment
The ideal workflow for maintaining the site and pushing updates to your server involves running the system tests and notifying the server of the updated software so that it can retrieve the updates.

If you are using Laravel Forge, it provides an automated deployment process which you can use out-of-the-box. (You may want to update the deploy script to also restart the queues, etc, if you are using them.)

## Optimal Workflow
Ideally you will have your site's files in a private Github repository.

When you make a commit to Github, the Github Actions scripts will be triggered and run the test suite.

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
- Provision a new server with Forge
- Add the Site
- Connect it to your Github repository
- You might want to update the Environment file with custom settings
- You might want to update the Deploy script in Forge using steps found in the `/deploy-laravel.sh` file in your app.
- If you want the tests suite to run successfully before new deployments, use the Deployment instructions above. However, if you just want Forge to always push every new commit directly to your site without running any tests, you can enable the Auto Deploy option in Forge.

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
