# Package Builder

[![Build Status](https://img.shields.io/travis/Symplify/PackageBuilder.svg?style=flat-square)](https://travis-ci.org/Symplify/PackageBuilder)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/Symplify/PackageBuilder.svg?style=flat-square)](https://scrutinizer-ci.com/g/Symplify/PackageBuilder)
[![Downloads](https://img.shields.io/packagist/dt/symplify/package-builder.svg?style=flat-square)](https://packagist.org/packages/symplify/package-builder)

*Write package once and let many other frameworks use it.*

This tools helps you to build package integrations to Laravel, Symfony and Nette, without any knowledge of their Dependency Injection components.

## Install

```bash
composer require symplify/package-builder
```


## Usage in Nette

### Create Container from Config

```php
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

$container = (new GeneralContainerFactory)->createFromConfig(
    __DIR__ . '/../src/config/config.neon'
);
```

That's all :)
