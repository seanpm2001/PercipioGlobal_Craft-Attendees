<?php
namespace percipiolondon\attendees\services;

use Craft;

use GuzzleHttp\Client;
use yii\base\Component;

class Metaseed extends Component
{
    public function school(String $query): ?\stdClass
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

        return null;
    }

    public function attendeeSchools($urns): ?array
    {
        try {
            $endpoint = 'https://api.v2.metaseed.io/urns';
            $client = new Client();

            $response = $client->post(
                $endpoint,
                [
                    'form_params' => array(
                        'orgUrn' => $urns
                    )
                ]
            );

            return json_decode($response->getBody()->getContents(), true);
        } catch(\Exception $e) {
            Craft::error("Something went wrong: {$e->getMessage()}", __METHOD__);
        }

        return null;
    }
}
