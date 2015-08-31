# ZipRecruiter Jobs Client

[![Latest Version](https://img.shields.io/github/release/JobBrander/jobs-ziprecruiter.svg?style=flat-square)](https://github.com/JobBrander/jobs-ziprecruiter/releases)
[![Software License](https://img.shields.io/badge/license-APACHE%202.0-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/JobBrander/jobs-ziprecruiter/master.svg?style=flat-square&1)](https://travis-ci.org/JobBrander/jobs-ziprecruiter)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/JobBrander/jobs-ziprecruiter.svg?style=flat-square)](https://scrutinizer-ci.com/g/JobBrander/jobs-ziprecruiter/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/JobBrander/jobs-ziprecruiter.svg?style=flat-square)](https://scrutinizer-ci.com/g/JobBrander/jobs-ziprecruiter)
[![Total Downloads](https://img.shields.io/packagist/dt/jobbrander/jobs-ziprecruiter.svg?style=flat-square)](https://packagist.org/packages/jobbrander/jobs-ziprecruiter)

This package provides [ZipRecruiter Jobs API](https://www.ziprecruiter.com/publishers)
support for the JobBrander's [Jobs Client](https://github.com/JobBrander/jobs-common).

## Installation

To install, use composer:

```
composer require jobbrander/jobs-ziprecruiter
```

## Usage

Usage is the same as Job Branders's Jobs Client, using `\JobBrander\Jobs\Client\Provider\Ziprecruiter` as the provider.

```php
$client = new JobBrander\Jobs\Client\Provider\Ziprecruiter([
    'apiKey' => 'YOUR ZIPRECRUITER API KEY'
]);

// Search for 200 job listings for 'project manager' in Chicago, IL
$jobs = $client->setKeyword('project manager')
    ->setCity('Chicago')
    ->setState('IL')
    ->setCount(200)
    ->getJobs();
```

The `getJobs` method will return a [Collection](https://github.com/JobBrander/jobs-common/blob/master/src/Collection.php) of [Job](https://github.com/JobBrander/jobs-common/blob/master/src/Job.php) objects.

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/jobbrander/jobs-ziprecruiter/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Karl Hughes](https://github.com/karllhughes)
- [All Contributors](https://github.com/jobbrander/jobs-ziprecruiter/contributors)


## License

The Apache 2.0. Please see [License File](https://github.com/jobbrander/jobs-ziprecruiter/blob/master/LICENSE) for more information.
