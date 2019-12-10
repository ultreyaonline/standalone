<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeployGitOnCloudwaysServersCommand extends Command
{
    protected $signature = 'deploy:cloudways_git';

    protected $description = 'Trigger the git portion of a deploy on Cloudways hosting';

    const CLOUDWAYS_API_URL = "https://api.cloudways.com/api/v1";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (empty(env('CLOUDWAYS_API_KEY'))) {
            return $this->error("The CLOUDWAYS .env settings are not set.\nThe following are required:\n
// Email is the account login email address\n
CLOUDWAYS_EMAIL=\n
// API Key can be obtained from platform.cloudways.com/api on paid accounts\n
CLOUDWAYS_API_KEY=\n
// Server ID can be found from the URL when looking at the server details\n
CLOUDWAYS_SERVER_ID=\n
// Application ID can be found from the URL when looking at the Application Settings\n
CLOUDWAYS_APP_ID=\n
// The GIT URL is your github URL. Best to use the SSH version of it, found on the Clone To Desktop dropdown\n
CLOUDWAYS_GIT_URL=\n
// The Branch is usually master\n
CLOUDWAYS_GIT_BRANCH=master\n

Optionally, you may want to run php artisan optimize:clear to clear out any old cached configs, and then rerun this command.
");
        }

        // Fetch API Access Token
        $tokenResponse = $this->callCloudwaysAPI('POST', '/oauth/access_token', null
            , [
                'email' => env('CLOUDWAYS_EMAIL'),
                'api_key' => env('CLOUDWAYS_API_KEY'),
            ]);

        $accessToken = $tokenResponse->access_token;

        $gitPullResponse = $this->callCloudWaysAPI('POST', '/git/pull', $accessToken, [
            'server_id' => env('CLOUDWAYS_SERVER_ID'),
            'app_id' => env('CLOUDWAYS_APP_ID'),
            'git_url' => env('CLOUDWAYS_GIT_URL'),
            'branch_name' => env('CLOUDWAYS_GIT_BRANCH'),
            'deploy_path' => '', // Cloudways Laravel always deploys to public_html, so we leave this blank
        ]);

        $pullResult = $gitPullResponse;

        // poll for status, in order to finish as quickly as possible
        if (!empty($id = $pullResult->operation_id)) {
            $i = 0;
            while ($i < 100) {
                $statusResponse = $this->callCloudWaysAPI('GET', '/operation/' . $id, $accessToken);
                if (!empty($statusResponse->operation->is_completed)) {
                    $i = 500;
                    continue;
                }
                usleep(500);
                $i++;
            }
        }

        $this->info(json_encode($gitPullResponse));
    }

    // contact Cloudways API
    protected function callCloudwaysAPI($method, $url, $accessToken, $post = [])
    {
        $baseURL = static::CLOUDWAYS_API_URL;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_URL, $baseURL . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //Set Authorization Header
        if ($accessToken) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        }

        //Set Post Parameters
        $encoded = '';
        if (count($post)) {
            foreach ($post as $name => $value) {
                $encoded .= urlencode($name) . '=' . urlencode($value) . '&';
            }
            $encoded = substr($encoded, 0, strlen($encoded) - 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpcode != '200') {
            $this->error('An error occurred. Code: ' . $httpcode . ' Output: ' . substr($output, 0, 10000));
            return [];
        }
        return json_decode($output);
    }

}
