<?php


namespace BABA\Collabim\API\Client;


use BABA\JSON\API\Client\DataProvider\CURL;
use BABA\JSON\API\Client\IDataProvider;
use BABA\JSON\API\Client\JsonRestApiClient;

class Collabim
{
    const ONETIMEANALYSESKEYWORDMEASURING_URL = 'https://api.oncollabim.com/ota/keyword-measuring';

    /**
     * @var string[]
     */
    private $headers = [
        'Accept:application/collabim+json',
        'Content-Type:application/json'
    ];

    private $isAuthenticated = false;
    private JsonRestApiClient $client;

    /**
     * Collabim constructor.
     * @param string[] $headers
     */
    public function __construct(array $headers = [])
    {
        $this->headers = array_unique(array_merge($this->headers, $headers));
        $this->client = new JsonRestApiClient(new CURL());
    }

    public function authenticate($apiKey) {
        if(!empty($apiKey)) {
            $this->headers[] = 'Authorization:' . $apiKey;
            $this->isAuthenticated = true;
        } else {
            throw new \Exception('ApiKey is mandatory.');
        }
    }

    /**
     * @param $geoId
     * @param array $keywords
     * @param int $searchEngine
     * @param int $priority
     * @return mixed
     */
    public function oneTimeAnalysesKeywordMeasuring($geoId, $keywords = [], $searchEngine = 1, $priority = 1) {
        if($this->isAuthenticated) {

            $request = new \stdClass();
            $request->keyword_measuring_form = new \stdClass();
            $request->keyword_measuring_form->searchEngine = $searchEngine;
            $request->keyword_measuring_form->geoId = $geoId;
            $request->keyword_measuring_form->priority = $priority;
            $request->keyword_measuring_form->desiredResultType = 'json';
            $request->keyword_measuring_form->keywords = $keywords;

            return $this->client->post(self::ONETIMEANALYSESKEYWORDMEASURING_URL, json_encode($request), false, $this->headers);
        } else {
            throw new Exception('You are not authenticated.');
        }
    }
}