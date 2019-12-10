<?php

return [
    /**
     * You should set a DEPLOY_SECRET_KEY in your .env file
     * This string value will be used to validate any incoming deploy hooks
     * to make sure they come from an authorized source, such as Github
     * or another Continuous Integration test suite 
     *
     * If the value is not set then any deploy hook calls will be aborted.
     */
    'deploy_secret_key' => env('DEPLOY_SECRET_KEY', null),
    
];
