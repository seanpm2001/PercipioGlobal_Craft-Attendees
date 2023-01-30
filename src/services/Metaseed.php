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

            // $curl = curl_init();

            // curl_setopt_array($curl, array(
            //     CURLOPT_URL => 'https://api.v2.metaseed.io/urns',
            //     CURLOPT_RETURNTRANSFER => true,
            //     CURLOPT_ENCODING => '',
            //     CURLOPT_MAXREDIRS => 10,
            //     CURLOPT_TIMEOUT => 0,
            //     CURLOPT_FOLLOWLOCATION => true,
            //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            //     CURLOPT_CUSTOMREQUEST => 'POST',
            //     CURLOPT_POSTFIELDS => array('orgUrn' => implode(', ', array_filter($urns))),
            // ));

            // $response = curl_exec($curl);

            // return json_decode($response, true);
        } catch(\Exception $e) {
            Craft::error("Something went wrong: {$e->getMessage()}", __METHOD__);
        }

        return null;
    }
}
