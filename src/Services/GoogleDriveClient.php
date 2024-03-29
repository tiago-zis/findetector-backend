<?php

namespace App\Services;

use Google\Service\Drive;
use Google\Client;

class GoogleDriveClient 
{

    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    public function getClient()
    {
        $secret = json_decode($_ENV['GOOGLE_CLIENT_SECRET'], true);
        $token = json_decode($_ENV['GOOGLE_TOKEN'], true);
        $client = new Client($secret && isset($secret['web']) ? $secret['web'] : []);
        $client->setScopes(Drive::DRIVE);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');        

        if ($token) {
            $client->setAccessToken($token);
        }        
        
        return $client;
    }
}
