<?php namespace JobApis\Jobs\Client\Queries;

class ZiprecruiterQuery extends AbstractQuery
{
    /**
     * api_key
     *
     * @var string
     */
    protected $api_key;

    /**
     * search
     *
     * The search query.
     *
     * @var string
     */
    protected $search;

    /**
     * location
     *
     * e.g., “Santa Monica, CA” or “London, UK”
     *
     * @var string
     */
    protected $location;

    /**
     * radius_miles
     *
     * @var integer
     */
    protected $radius_miles;

    /**
     * page
     *
     * @var integer
     */
    protected $page;

    /**
     * jobs_per_page
     *
     * @var integer
     */
    protected $jobs_per_page;

    /**
     * days_ago
     *
     * @var integer
     */
    protected $days_ago;

    /**
     * Get baseUrl
     *
     * @return  string Value of the base url to this api
     */
    public function getBaseUrl()
    {
        return 'https://api.ziprecruiter.com/jobs/v1';
    }

    /**
     * Get keyword
     *
     * @return  string Attribute being used as the search keyword
     */
    public function getKeyword()
    {
        return $this->search;
    }

    /**
     * Required parameters
     *
     * @return array
     */
    protected function requiredAttributes()
    {
        return [
            'api_key',
        ];
    }
}
