# SeoBundle

Symfony based seo bundle to crawl websites.

## Installation

**Install dependency with composer**

```bash
composer require l91/seo-bundle:dev-master
```

**Add it to your Kernel**

```php
new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
new L91\Bundle\SeoBundle\L91SeoBundle(),
```

**Add new routes**

```yml
l91_seo:
    resource: '@L91SeoBundle/Resources/config/routing.xml'
    prefix: /crawler
```

**Gedmo Tree Extension Configuration**

```yml
doctrine:
    orm:
        mappings:
            gedmo_tree:
                type: xml
                prefix: Gedmo\Tree\Entity
                dir: "%kernel.root_dir%/../vendor/gedmo/doctrine-extensions/lib/Gedmo/Tree/Entity"
                alias: GedmoTree
                is_bundle: false

stof_doctrine_extensions:
    orm:
        default:
          tree: true
```

**Create database schema**

```bash
bin/console doctrine:schema:update
```

## Usage

```
bin/console l91:seo:crawl http://www.example.org 
```
