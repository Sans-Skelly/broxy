# Broxy
![status-release](https://img.shields.io/badge/status-release-green.svg) ![version-0-1-0](https://img.shields.io/badge/semantic_version-1.0.0-blue.svg)

A simple transparent web proxy written with PHP & cURL with basic cookie support

## Getting Started

### Prerequisites

 * PHP 5.6+ with cURL

### Installing

 * Configure __Broxy__ by editing `config.json`
```json
{
    "host": "https://beremaran.com",
    "targetHost": "http://localhost:8000"
}```
 * Copy `.htaccess` file
 * Give read/write permissions to `cookies` folder
 * Done.

## Contributing
Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning
We use [SemVer](http://semver.org/) for versioning.

## Authors

 * __Berke Emrecan Arslan__ - [beremaran](https://github.com/beremaran)


## License
This project is licensed under MIT license. See [LICENSE](LICENSE) for details
