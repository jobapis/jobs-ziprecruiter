<?php namespace JobApis\Jobs\Client\Providers\Test;

use JobApis\Jobs\Client\Collection;
use JobApis\Jobs\Client\Job;
use JobApis\Jobs\Client\Providers\ZiprecruiterProvider;
use JobApis\Jobs\Client\Queries\ZiprecruiterQuery;
use Mockery as m;

class ZiprecruiterProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->query = m::mock('JobApis\Jobs\Client\Queries\ZiprecruiterQuery');

        $this->client = new ZiprecruiterProvider($this->query);
    }

    public function testItCanGetDefaultResponseFields()
    {
        $fields = [
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
        $this->assertEquals($fields, $this->client->getDefaultResponseFields());
    }

    public function testItCanGetListingsPath()
    {
        $this->assertEquals('jobs', $this->client->getListingsPath());
    }

    public function testItCanCreateJobObjectFromPayload()
    {
        $payload = $this->createJobArray();

        $results = $this->client->createJobObject($payload);

        $this->assertInstanceOf(Job::class, $results);
        $this->assertEquals($payload['name'], $results->getTitle());
        $this->assertEquals($payload['name'], $results->getName());
        $this->assertEquals($payload['snippet'], $results->getDescription());
        $this->assertEquals($payload['hiring_company']['name'], $results->getCompanyName());
        $this->assertEquals($payload['url'], $results->getUrl());
        $this->assertEquals($payload['job_age'], $results->job_age);
        $this->assertEquals($payload['posted_time_friendly'], $results->posted_time_friendly);
        $this->assertEquals($payload['has_non_zr_url'], $results->has_non_zr_url);
    }

    /**
     * Integration test for the client's getJobs() method.
     */
    public function testItCanGetJobs()
    {
        $options = [
            'search' => uniqid(),
            'location' => uniqid(),
            'api_key' => uniqid(),
        ];

        $guzzle = m::mock('GuzzleHttp\Client');

        $query = new ZiprecruiterQuery($options);

        $client = new ZiprecruiterProvider($query);

        $client->setClient($guzzle);

        $response = m::mock('GuzzleHttp\Message\Response');

        $jobs = ['jobs' => [
                $this->createJobArray(),
                $this->createJobArray(),
            ],
        ];

        $guzzle->shouldReceive('get')
            ->with($query->getUrl(), [])
            ->once()
            ->andReturn($response);
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn(json_encode($jobs));

        $results = $client->getJobs();

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertCount(count($jobs['jobs']), $results);
    }

    /**
     * Integration test with actual API call to the provider.
     */
    public function testItCanGetJobsFromApi()
    {
        if (!getenv('API_KEY')) {
            $this->markTestSkipped('API_KEY not set. Real API call will not be made.');
        }

        $keyword = 'engineering';

        $query = new ZiprecruiterQuery([
            'search' => $keyword,
            'api_key' => getenv('API_KEY'),
        ]);

        $client = new ZiprecruiterProvider($query);

        $results = $client->getJobs();

        $this->assertInstanceOf('JobApis\Jobs\Client\Collection', $results);

        foreach($results as $job) {
            $this->assertEquals($keyword, $job->query);
        }
    }

    private function createJobArray() {
        return [
            'source' => uniqid(),
            'id' => uniqid(),
            'name' => uniqid(),
            'location' => uniqid(),
            'snippet' => uniqid(),
            'category' => uniqid(),
            'hiring_company' => [
                'url' => uniqid(),
                'name' => uniqid(),
            ],
            'posted_time' => '2015-'.rand(1,12).'-'.rand(1,31),
            'url' => uniqid(),
            'city' => null,
            'state' => null,
            'job_age' => rand(1, 100),
            'posted_time_friendly' => uniqid(),
            'has_non_zr_url' => rand(0, 1),
        ];
    }
}
