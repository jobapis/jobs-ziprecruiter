<?php namespace JobApis\Jobs\Client\Providers\Test;

use JobBrander\Jobs\Client\Providers\Ziprecruiter;
use Mockery as m;

class ZiprecruiterTest extends \PHPUnit_Framework_TestCase
{
    private $clientClass = 'JobBrander\Jobs\Client\Providers\AbstractProvider';
    private $collectionClass = 'JobBrander\Jobs\Client\Collection';
    private $jobClass = 'JobBrander\Jobs\Client\Job';

    public function setUp()
    {
        $this->params = [
            'api_key' => '12345667'
        ];
        $this->client = new Ziprecruiter($this->params);
    }

    public function testItWillUseJsonFormat()
    {
        $format = $this->client->getFormat();

        $this->assertEquals('json', $format);
    }

    public function testItWillUseGetHttpVerb()
    {
        $verb = $this->client->getVerb();

        $this->assertEquals('GET', $verb);
    }

    public function testListingPath()
    {
        $path = $this->client->getListingsPath();

        $this->assertEquals('jobs', $path);
    }

    public function testUrlIncludesKeywordWhenProvided()
    {
        $keyword = uniqid().' '.uniqid();
        $param = 'search='.urlencode($keyword);

        $url = $this->client->setKeyword($keyword)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesKeywordWhenNotProvided()
    {
        $param = 'search=';

        $url = $this->client->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesLocationWhenCityAndStateProvided()
    {
        $city = uniqid();
        $state = uniqid();
        $param = 'location='.urlencode($city.', '.$state);

        $url = $this->client->setLocation($city.', '.$state)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesLocationWhenCityProvided()
    {
        $city = uniqid();
        $param = 'location='.urlencode($city);

        $url = $this->client->setLocation($city)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesLocationWhenStateProvided()
    {
        $state = uniqid();
        $param = 'location='.urlencode($state);

        $url = $this->client->setLocation($state)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesLocationWhenNotProvided()
    {
        $param = 'location=';

        $url = $this->client->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesLimitWhenProvided()
    {
        $limit = uniqid();
        $param = 'jobs_per_page='.$limit;

        $url = $this->client->setCount($limit)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesLimitWhenNotProvided()
    {
        $param = 'jobs_per_page=';

        $url = $this->client->setCount(null)->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testUrlIncludesApiKeyWhenProvided()
    {
        $param = 'api_key='.$this->params['api_key'];

        $url = $this->client->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlIncludesStartWhenProvided()
    {
        $page = uniqid();
        $param = 'page='.$page;

        $url = $this->client->setPage($page)->getUrl();

        $this->assertContains($param, $url);
    }

    public function testUrlNotIncludesStartWhenNotProvided()
    {
        $param = '&page=';

        $url = $this->client->setPage(null)->getUrl();

        $this->assertNotContains($param, $url);
    }

    public function testItCanCreateJobFromPayload()
    {
        $payload = $this->createJobArray();
        $results = $this->client->createJobObject($payload);

        $this->assertEquals($payload['name'], $results->title);
        $this->assertEquals($payload['snippet'], $results->description);
        $this->assertEquals($payload['hiring_company']['name'], $results->company);
        $this->assertEquals($payload['url'], $results->url);
        $this->assertEquals($payload['id'], $results->sourceId);
        $this->assertEquals($payload['location'], $results->location);
    }

    public function testItCanConnect()
    {
        $provider = $this->getProviderAttributes();

        for ($i = 0; $i < $provider['jobs_count']; $i++) {
            $payload['jobs'][] = $this->createJobArray();
        }

        $responseBody = json_encode($payload);

        $job = m::mock($this->jobClass);
        $job->shouldReceive('setQuery')->with($provider['keyword'])
            ->times($provider['jobs_count'])->andReturnSelf();
        $job->shouldReceive('setSource')->with($provider['source'])
            ->times($provider['jobs_count'])->andReturnSelf();

        $response = m::mock('GuzzleHttp\Message\Response');
        $response->shouldReceive('getBody')->once()->andReturn($responseBody);

        $http = m::mock('GuzzleHttp\Client');
        $http->shouldReceive(strtolower($this->client->getVerb()))
            ->with($this->client->getUrl(), $this->client->getHttpClientOptions())
            ->once()
            ->andReturn($response);
        $this->client->setClient($http);

        $results = $this->client->getJobs();

        $this->assertInstanceOf($this->collectionClass, $results);
        $this->assertCount($provider['jobs_count'], $results);
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
            'posted_time' => '2015-07-'.rand(1,31),
            'url' => uniqid(),
        ];
    }

    private function getProviderAttributes($attributes = [])
    {
        $defaults = [
            'path' => uniqid(),
            'format' => 'json',
            'keyword' => uniqid(),
            'source' => uniqid(),
            'params' => [uniqid()],
            'jobs_count' => rand(2,10),
        ];
        return array_replace($defaults, $attributes);
    }
}
