<?php namespace JobBrander\Jobs\Client\Providers;

use JobBrander\Jobs\Client\Job;

class Ziprecruiter extends AbstractProvider
{
    /**
     * API Key
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Query params
     *
     * @var array
     */
    protected $queryParams = [];

    /**
     * Add query params, if valid
     *
     * @param string $value
     * @param string $key
     *
     * @return  void
     */
    private function addToQueryStringIfValid($value, $key)
    {
        $computed_value = $this->$value();
        if (!is_null($computed_value)) {
            $this->queryParams[$key] = $computed_value;
        }
    }

    /**
     * Returns the standardized job object
     *
     * @param array $payload
     *
     * @return \JobBrander\Jobs\Client\Job
     */
    public function createJobObject($payload)
    {
        $defaults = [
            'source',
            'id',
            'name',
            'snippet',
            'category',
            'posted_time',
            'posted_time_friendly',
            'url',
            'location',
            'city',
            'state',
            'country',
            'hiring_company',
        ];

        $payload = static::parseAttributeDefaults($payload, $defaults);

        $job = new Job([
            'title' => $payload['name'],
            'name' => $payload['name'],
            'description' => $payload['snippet'],
            'url' => $payload['url'],
            'sourceId' => $payload['id'],
            'location' => $payload['location'],
        ]);

        $job->setCompany($payload['hiring_company'])
            ->setDatePostedAsString($payload['posted_time'])
            ->setCity($payload['city'])
            ->setState($payload['state']);

        return $job;
    }

    /**
     * Get data format
     *
     * @return string
     */
    public function getFormat()
    {
        return 'json';
    }

    /**
     * Get listings path
     *
     * @return  string
     */
    public function getListingsPath()
    {
        return 'jobs';
    }

    /**
     * Get combined location
     *
     * @return string
     */
    public function getLocation()
    {
        $location = ($this->city ? $this->city.', ' : null).($this->state ?: null);

        if ($location) {
            return $location;
        }

        return null;
    }

    /**
     * Get query string for client based on properties
     *
     * @return string
     */
    public function getQueryString()
    {
        $query_params = [
            'api_key' => 'getApiKey',
            'search' => 'getKeyword',
            'location' => 'getLocation',
            'page' => 'getPage',
            'jobs_per_page' => 'getCount',
        ];

        array_walk($query_params, [$this, 'addToQueryStringIfValid']);

        return http_build_query($this->queryParams);
    }

    /**
     * Get url
     *
     * @return  string
     */
    public function getUrl()
    {
        $query_string = $this->getQueryString();

        return 'https://api.ziprecruiter.com/jobs/v1?'.$query_string;
    }

    /**
     * Get http verb
     *
     * @return  string
     */
    public function getVerb()
    {
        return 'GET';
    }
}
