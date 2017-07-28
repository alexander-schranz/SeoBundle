# SeoBundle

Symfony based seo bundle to crawl websites.

## Installation

**Install dependency with composer**

```bash
composer require l91/seo-bundle:dev-master
```

**Add it to your Kernel**

```
new L91\Bundle\SeoBundle\L91SeoBundle(),
```

## Usage

```
bin/console l91:seo:crawl http://www.example.org 
```

