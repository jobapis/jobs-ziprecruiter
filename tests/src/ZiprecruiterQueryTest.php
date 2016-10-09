<?php namespace JobApis\Jobs\Client\Test;

use JobApis\Jobs\Client\Queries\ZiprecruiterQuery;
use Mockery as m;

class JujuQueryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->query = new ZiprecruiterQuery();
    }

    public function testItCanGetBaseUrl()
    {
        $this->assertEquals(
            'https://api.ziprecruiter.com/jobs/v1',
            $this->query->getBaseUrl()
        );
    }

    public function testItCanGetKeyword()
    {
        $keyword = uniqid();
        $this->query->set('search', $keyword);
        $this->assertEquals($keyword, $this->query->getKeyword());
    }

    public function testItReturnsFalseIfRequiredAttributesMissing()
    {
        $this->assertFalse($this->query->isValid());
    }

    public function testItReturnsTrueIfRequiredAttributesPresent()
    {
        $this->query->set('api_key', uniqid());

        $this->assertTrue($this->query->isValid());
    }

    public function testItCanAddAttributesToUrl()
    {
        $this->query->set('api_key', uniqid());
        $this->query->set('search', uniqid());

        $url = $this->query->getUrl();

        $this->assertContains('api_key=', $url);
        $this->assertContains('search=', $url);
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testItThrowsExceptionWhenSettingInvalidAttribute()
    {
        $this->query->set(uniqid(), uniqid());
    }

    /**
     * @expectedException OutOfRangeException
     */
    public function testItThrowsExceptionWhenGettingInvalidAttribute()
    {
        $this->query->get(uniqid());
    }

    public function testItSetsAndGetsValidAttributes()
    {
        $attributes = [
            'search' => uniqid(),
            'location' => uniqid(),
            'radius_miles' => rand(1,100),
        ];

        foreach ($attributes as $key => $value) {
            $this->query->set($key, $value);
        }

        foreach ($attributes as $key => $value) {
            $this->assertEquals($value, $this->query->get($key));
        }
    }
}
