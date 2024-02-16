# NextSign PHP
A php client to use NextSign's API within a PHP project.

------

**NextSignClient** allows you to :
- use case
- use case
- use case

DTO def missing
------

## Usage

------

## Installation

To install NextSign-php via [composer](https://getcomposer.org/) simply use the following command:  

```shell
composer require logipro/nextsign-php
```

------


## To contribute to Datamaps PHP
### Requirements:
* docker
* git

### Unit tests
```shell
bin/phpunit
```

### Integration tests
```shell
bin/phpunit-integration
```

### Quality
#### Some indicators:
* phpcs PSR12
* phpstan level 9
* coverage >= 100%
* infection MSI >= 100%


#### Quick check with:
```shell
./codecheck
```


#### Check coverage with:
```shell
bin/phpunit
```
and view 'var/coverage/index.html' in your browser


#### Check infection with:
```shell
bin/infection
```
and view 'var/infection.html' in your browser