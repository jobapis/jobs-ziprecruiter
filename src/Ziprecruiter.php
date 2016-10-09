<?php namespace JobApis\Jobs\Client\Providers;

use JobBrander\Jobs\Client\Job;

class Ziprecruiter extends AbstractProvider
{
    /**
     * Map of setter methods to query parameters
     *
     * @var array
     */
    protected $queryMap = [
        'setApiKey' => 'api_key',
        'setSearch' => 'search',
        'setLocation' => 'location',
        'setRadiusMiles' => 'radius_miles',
        'setPage' => 'page',
        'setJobsPerPage' => 'jobs_per_page',
        'setDaysAgo' => 'days_ago',
        'setKeyword' => 'search',
        'setCount' => 'jobs_per_page',
    ];

    /**
     * Current api query parameters
     *
     * @var array
     */
    protected $queryParams = [
        'api_key' => null,
        'search' => null,
        'location' => null,
        'radius_miles' => null,
        'page' => null,
        'jobs_per_page' => null,
        'days_ago' => null,
    ];

    /**
     * Create new Ziprecruiter jobs client.
     *
     * @param array $parameters
     */
    public function __construct($parameters = [])
    {
        parent::__construct($parameters);
        array_walk($parameters, [$this, 'updateQuery']);
    }

    /**
     * Magic method to handle get and set methods for properties
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (isset($this->queryMap[$method], $parameters[0])) {
            $this->updateQuery($parameters[0], $this->queryMap[$method]);
        }
        return parent::__call($method, $parameters);
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

        $job->setCompany($payload['hiring_company']['name'])
            ->setCompanyUrl($payload['hiring_company']['url'])
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
     * Get query string for client based on properties
     *
     * @return string
     */
    public function getQueryString()
    {
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

    /**
     * Attempts to update current query parameters.
     *
     * @param  string  $value
     * @param  string  $key
     *
     * @return Careerbuilder
     */
    protected function updateQuery($value, $key)
    {
        if (array_key_exists($key, $this->queryParams)) {
            $this->queryParams[$key] = $value;
        }
        return $this;
    }
}
