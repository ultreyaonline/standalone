<?php

namespace App\Http\Controllers\Utils;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class DeploymentController extends Controller
{
    public function handle(Request $request)
    {
        $localToken = config('deployment.deploy_secret_key');
        if (empty($localToken)) {
            // if token key is not set in .env then we silently abort, acknowledging the webhook call but taking no action
            return 'No local token found. Ignoring.';
        }

        // GITHUB
        if ($githubHash = $request->header('X-Hub-Signature')) {
            $githubPayload = $request->getContent();
            $localHash = 'sha1=' . hash_hmac('sha1', $githubPayload, $localToken, false);
            if (!hash_equals($githubHash, $localHash)) {
                abort(403, 'Unauthorized: Hmac validation failed. Is the token properly set?');
            }

            $payload = json_decode($githubPayload, true);

            if (!isset($payload['action'])) {
                info(print_r($payload, true));
                abort(501, 'Could not parse payload. See server logs.');
            }

            // respond only to completed Github Actions payload (assumes the only Action set up is a CI test-suite run)
            if (
                $payload['action'] === 'completed'
                && $payload['check_suite']['status'] === 'completed'
                && $payload['check_suite']['conclusion'] === 'success'
            ) {
                $this->deploy();
                return 'Deployed.';
            } else {
                return 'Not an action to which this endpoint responds. Ignoring.';
            }
        }

        // GITLAB
        if (!empty($request->header('X-Gitlab-Token'))) {
            if ($request->header('X-Gitlab-Token') != $localToken) {
                abort(403, 'Gitlab token does not match local token');
            }

            $this->deploy();
            return 'Deployed.';
        }
    }

    /**
     * Do actual deployment
     * NOTE: Default timeout is 60 seconds for Process(). BUT webhooks want an immediate reply, so answer back quickly!
     */
    public function deploy()
    {
        $root_path = base_path();
        $process = new Process('sh ./deploy-laravel.sh', $root_path);
        echo 'Received.';
        $process->run(function ($type, $buffer) {
            info($buffer ?? '');
            echo $buffer;
        });
    }
}
