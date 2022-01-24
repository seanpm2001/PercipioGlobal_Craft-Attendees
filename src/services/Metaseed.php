<?php
namespace percipiolondon\attendees\services;

use Craft;

use GuzzleHttp\Client;
use yii\base\Component;

class Metaseed extends Component
{
    public function school(String $query): \stdClass
    {
        //$this->metaseed->school()

        try {
            $endpoint = 'https://api.v2.metaseed.io/schools/?mode=legacy&query='.$query;
            $client = new Client();

            $response = $client->get($endpoint);
            $result = $response->getBody()->getContents();

            return json_decode($result);

        } catch(\Exception $e) {
            Craft::error("Something went wrong: {$e->getMessage()}", __METHOD__);
        }
    }
}
