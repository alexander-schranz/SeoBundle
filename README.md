# SeoBundle

Symfony based seo bundle to crawl websites.

## Installation

**Install dependency with composer**

```bash
composer require l91/seo-bundle:dev-master
```

**Add it to your Kernel**

```php
new L91\Bundle\SeoBundle\L91SeoBundle(),
```

**Create database schema**

```bash
bin/console doctrine:schema:update
```

## Usage

```
bin/console l91:seo:crawl http://www.example.org 
```
