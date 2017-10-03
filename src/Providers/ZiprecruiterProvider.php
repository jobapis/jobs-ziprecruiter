<?php namespace JobApis\Jobs\Client\Providers;

use JobApis\Jobs\Client\Job;

class ZiprecruiterProvider extends AbstractProvider
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
     * Returns the standardized job object
     *
     * @param array $payload
     *
     * @return \JobApis\Jobs\Client\Job
     */
    public function createJobObject($payload)
    {
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

        $job->job_age = $payload['job_age'];
        $job->posted_time_friendly = $payload['posted_time_friendly'];
        $job->has_non_zr_url = $payload['has_non_zr_url'];

        return $job;
    }

    /**
     * Job response object default keys that should be set
     *
     * @return  array
     */
    public function getDefaultResponseFields()
    {
        return [
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
}
