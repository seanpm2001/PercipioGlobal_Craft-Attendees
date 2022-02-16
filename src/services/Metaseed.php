<?php
namespace percipiolondon\attendees\services;

use Craft;

use GuzzleHttp\Client;
use yii\base\Component;

class Metaseed extends Component
{
    public function school(String $query): \stdClass
    {
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

    public function attendeeSchools($urns): \stdClass
    {
        try {
            $endpoint = 'https://api.v2.metaseed.io/urns';
            $client = new Client();

            $response = $client->request('post', $endpoint, [
                'orgUrn' => json_encode($urns)
            ]);
            $result = $response->getBody()->getContents();

            return json_decode($result);
        } catch(\Exception $e) {
            Craft::error("Something went wrong: {$e->getMessage()}", __METHOD__);
        }
    }
}
