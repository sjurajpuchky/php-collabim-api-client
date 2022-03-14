<?php


namespace BABA\Collabim\API\Client;


use BABA\JSON\API\Client\DataProvider\CURL;
use BABA\REST\API\Client\DataProvider\CURL as RESTCURL;
use BABA\JSON\API\Client\JsonRestApiClient;
use BABA\REST\API\Client\RestApiClient;
use Exception;
use stdClass;

class Collabim
{
    const BASE_URL = 'https://api.oncollabim.com';
    const ONE_TIME_ANALYSES_KEYWORD_MEASURING_URL = '/ota/keyword-measuring';
    const GET_PROJECT_NFO_BY_ID = '/projects/';
    const GET_PROJECTS_LIST = '/projects';
    const GET_WIDGETS = '/projects/%s/widget/fullWidget';
    const GET_WIDGETS_JSON = '/projects/%s/widget/fullWidget/json';
    const GET_ACTIVITIES = '/activities?projectId=%s';
    const GET_ACTIVITY = '/activities/%s';
    const DELETE_ACTIVITY = '/activities/%s';
    const POST_ACTIVITY = '/activities';
    const PUT_ACTIVITY = '/activities/%s';
    const GET_KEYWORDS = '/keywords?projectId=%s';
    const GET_KEYWORDS_POSITION = '/keyword-positions?projectId=%s';
    const GET_KEYWORDS_POSITION_AGGREGATED = '/aggregated-keywords-positions?projectId=%s';
    const GET_POSITION_DISTRIBUTION = '/position-distribution?projectId=%s';
    const GET_INDEXED_PAGES = '/indexed-pages?projectId=%s';
    const GET_MARKET_SHARE = '/market-share?projectId=%s';



    /**
     * @var string[]
     */
    private array $headers = [
        'Accept:application/collabim+json',
        'Content-Type:application/json'
    ];

    private bool $isAuthenticated = false;
    private JsonRestApiClient $client;
    private RestApiClient $pureClient;
    private string $apiKey;

    /**
     * Collabim constructor.
     * @param string $configFile
     * @param string[] $headers
     * @throws Exception
     */
    public function __construct($configFile = 'config.ini', array $headers = [])
    {
        $ini = $this->parseIni($configFile);
        if (empty($ini['COLLABIM']['apiKey'])) {
            throw new Exception("apiKey is required in $configFile section [COLLABIM], you can request it on https://collabim.cz/?promoCode=mRfeciXH1V\n");
        } else {
            $this->apiKey = $ini['COLLABIM']['apiKey'];
        }

        $this->headers = array_unique(array_merge($this->headers, $headers));
        $this->client = new JsonRestApiClient(new CURL());
        $this->pureClient = new RestApiClient(new RESTCURL());
    }

    /**
     * @return bool
     */
    public function authenticate(): bool
    {
        if (!empty($this->apiKey)) {
            $this->headers[] = 'Authorization:' . $this->apiKey;
            return $this->isAuthenticated = true;
        }
        return false;
    }

    /**
     * @param $configFile
     * @return array
     * @throws Exception
     */
    public static function parseIni($configFile): array
    {
        if (file_exists($configFile)) {
            $ini = parse_ini_file($configFile, true);

            if (!$ini) {
                throw new Exception("Error parsing $configFile");
            }
        } else {

            throw new Exception("$configFile not found");
        }

        return $ini;
    }

    /**
     * @param $response
     * @return mixed
     * @throws Exception
     */
    private function getData($response)
    {
        if (isset($response->data)) {
            return $response->data;
        } elseif (isset($response->message)) {
            throw new Exception($response->message);
        } else {
            return $response;
        }
    }

    /**
     * @param $response
     * @return mixed
     * @throws Exception
     */
    private function getPureData($response)
    {
        if (!is_null($response)) {
            return $response;
        } else {
            throw new Exception("No data");
        }
    }


    /**
     * Get project info by id
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function projectGetInfoById($id)
    {
        if ($this->isAuthenticated) {
            return $this->getData($this->client->get(self::BASE_URL . self::GET_PROJECT_NFO_BY_ID . $id, false, $this->headers));
        } else {
            throw new Exception('You are not authenticated.');
        }
    }

    /**
     * Get project widgets HTML by project id
     * @param $projectId
     * @return mixed
     * @throws Exception
     */
    public function projectGetWidgetsHTML($projectId)
    {
        if ($this->isAuthenticated) {
            return $this->getPureData($this->pureClient->get(self::BASE_URL . sprintf(self::GET_WIDGETS, $projectId), false, $this->headers));
        } else {
            throw new Exception('You are not authenticated.');
        }
    }

    /**
     * Get project widgets object by project id
     * @param $projectId
     * @return mixed
     * @throws Exception
     */
    public function projectGetWidgets($projectId)
    {
        if ($this->isAuthenticated) {
            return $this->getData($this->client->get(self::BASE_URL . sprintf(self::GET_WIDGETS_JSON, $projectId), false, $this->headers));
        } else {
            throw new Exception('You are not authenticated.');
        }
    }

    /**
     * Get project activities info by project id
     * @param $projectId
     * @return mixed
     * @throws Exception
     */
    public function projectGetActivities($projectId)
    {
        if ($this->isAuthenticated) {
            return $this->getData($this->client->get(self::BASE_URL . sprintf(self::GET_ACTIVITIES, $projectId), false, $this->headers));
        } else {
            throw new Exception('You are not authenticated.');
        }
    }

    /**
     * Get activity info by id
     * @param $activityId
     * @return mixed
     * @throws Exception
     */
    public function projectGetActivity($activityId)
    {
        if ($this->isAuthenticated) {
            return $this->getData($this->client->get(self::BASE_URL . sprintf(self::GET_ACTIVITY, $activityId), false, $this->headers));
        } else {
            throw new Exception('You are not authenticated.');
        }
    }

    /**
     * Delete activity info by id
     * @param $activityId
     * @return mixed
     * @throws Exception
     */
    public function projectDeleteActivity($activityId)
    {
        if ($this->isAuthenticated) {
            return $this->getData($this->client->delete(self::BASE_URL . sprintf(self::DELETE_ACTIVITY, $activityId), false, $this->headers));
        } else {
            throw new Exception('You are not authenticated.');
        }
    }

    /**
     * Post project activity by project id
     * @param $projectId
     * @param $typeId
     * @param $stateId
     * @param $webId
     * @return mixed
     * @throws Exception
     */
    public function projectPostActivity($projectId,$typeId,$stateId,$webId)
    {

        $data = new stdClass();
        $data->projectId = $projectId;
        $data->typeId = $typeId;
        $data->stateId = $stateId;
        $data->webId = $webId;

        if ($this->isAuthenticated) {
            return $this->getData($this->client->post(self::BASE_URL . self::POST_ACTIVITY,json_encode($data), false, $this->headers));
        } else {
            throw new Exception('You are not authenticated.');
        }
    }

    /**
     * Post project activity by project id
     * @param $activityId
     * @param $projectId
     * @param $typeId
     * @param $stateId
     * @param $webId
     * @param $categoryId
     * @param $createdById
     * @param $note
     * @param $pageWithLinkUrl
     * @param $websiteUrl
     * @return mixed
     * @throws Exception
     */
    public function projectPutActivity($activityId,$projectId,$typeId,$stateId,$webId, $categoryId,$createdById,$note,$pageWithLinkUrl,$websiteUrl)
    {

        $data = new stdClass();
        $data->projectId = $projectId;
        $data->typeId = $typeId;
        $data->stateId = $stateId;
        $data->webId = $webId;
        $data->categoryId = $categoryId;
        $data->createdById = $createdById;
        $data->note = $note;
        $data->pageWithLinkUrl = $pageWithLinkUrl;
        $data->websiteUrl = $websiteUrl;

        if ($this->isAuthenticated) {
            return $this->getData($this->client->put(self::BASE_URL . sprintf(self::PUT_ACTIVITY,$activityId),json_encode($data), false, $this->headers));
        } else {
            throw new Exception('You are not authenticated.');
        }
    }

    /**
     * Get keywords by project id
     * @param $projectId
     * @return mixed
     * @throws Exception
     */
    public function projectGetKeywords($projectId)
    {
        if ($this->isAuthenticated) {
            return $this->getData($this->client->get(self::BASE_URL . sprintf(self::GET_KEYWORDS, $projectId), false, $this->headers));
        } else {
            throw new Exception('You are not authenticated.');
        }
    }

    /**
     * Get keywords position by project id
     * @param $projectId
     * @return mixed
     * @throws Exception
     */
    public function projectGetKeywordsPosition($projectId)
    {
        if ($this->isAuthenticated) {
            return $this->getData($this->client->get(self::BASE_URL . sprintf(self::GET_KEYWORDS_POSITION, $projectId), false, $this->headers));
        } else {
            throw new Exception('You are not authenticated.');
        }
    }

    /**
     * Get keywords aggregated position by project id
     * @param $projectId
     * @return mixed
     * @throws Exception
     */
    public function projectGetKeywordsAggregatedPosition($projectId)
    {
        if ($this->isAuthenticated) {
            return $this->getData($this->client->get(self::BASE_URL . sprintf(self::GET_KEYWORDS_POSITION_AGGREGATED, $projectId), false, $this->headers));
        } else {
            throw new Exception('You are not authenticated.');
        }
    }

    /**
     * Get position distribution by project id
     * @param $projectId
     * @return mixed
     * @throws Exception
     */
    public function projectGetPositionDistribution($projectId)
    {
        if ($this->isAuthenticated) {
            return $this->getData($this->client->get(self::BASE_URL . sprintf(self::GET_POSITION_DISTRIBUTION, $projectId), false, $this->headers));
        } else {
            throw new Exception('You are not authenticated.');
        }
    }

    /**
     * Get indexed pages by project id
     * @param $projectId
     * @return mixed
     * @throws Exception
     */
    public function projectGetIndexedPages($projectId)
    {
        if ($this->isAuthenticated) {
            return $this->getData($this->client->get(self::BASE_URL . sprintf(self::GET_INDEXED_PAGES, $projectId), false, $this->headers));
        } else {
            throw new Exception('You are not authenticated.');
        }
    }

    /**
     * Get market share by project id
     * @param $projectId
     * @return mixed
     * @throws Exception
     */
    public function projectGetMarketShare($projectId)
    {
        if ($this->isAuthenticated) {
            return $this->getData($this->client->get(self::BASE_URL . sprintf(self::GET_MARKET_SHARE, $projectId), false, $this->headers));
        } else {
            throw new Exception('You are not authenticated.');
        }
    }

    /**
     * Get project info by id
     * @return mixed
     * @throws Exception
     */
    public function projectsGetList()
    {
        if ($this->isAuthenticated) {
            return $this->getData($this->client->get(self::BASE_URL . self::GET_PROJECTS_LIST, false, $this->headers));
        } else {
            throw new Exception('You are not authenticated.');
        }
    }

    /**
     * @param $geoId
     * @param array $keywords
     * @param int $searchEngine
     * @param int $priority
     * @return mixed
     * @throws Exception
     */
    public function oneTimeAnalysesKeywordMeasuring($geoId, $keywords = [], $searchEngine = 1, $priority = 1)
    {
        if ($this->isAuthenticated) {

            $request = new stdClass();
            $request->keyword_measuring_form = new stdClass();
            $request->keyword_measuring_form->searchEngine = $searchEngine;
            $request->keyword_measuring_form->geoId = $geoId;
            $request->keyword_measuring_form->priority = $priority;
            $request->keyword_measuring_form->desiredResultType = 'json';
            $request->keyword_measuring_form->keywords = $keywords;

            return $this->getData($this->client->post(self::BASE_URL . self::ONE_TIME_ANALYSES_KEYWORD_MEASURING_URL, json_encode($request), false, $this->headers));
        } else {
            throw new Exception('You are not authenticated.');
        }
    }
}