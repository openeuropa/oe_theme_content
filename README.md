# OpenEuropa theme

[![Build Status](https://drone.fpfis.eu/api/badges/openeuropa/oe_theme_content/status.svg?branch=2.x)](https://drone.fpfis.eu/openeuropa/oe_theme_content)
[![Packagist](https://img.shields.io/packagist/v/openeuropa/oe_theme_content.svg)](https://packagist.org/packages/openeuropa/oe_theme_content)

Package of OpenEuropa 8 theme companion modules responsible for shipping OpenEuropa corporate content adaptations.

**Table of contents:**

- [Requirements](#requirements)
- [Installation](#installation)
- [Companion sub-modules](#companion-sub-modules)
- [Development](#development)
  - [Project setup](#project-setup)
  - [Using Docker Compose](#using-docker-compose)
  - [Disable Drupal 8 caching](#disable-drupal-8-caching)
  - [Working with ECL components](#working-with-ecl-components)
- [Contributing](#contributing)
- [Versioning](#versioning)

## Requirements

This depends on the following software:

* [PHP 7.2 or 7.3](http://php.net/)

## Installation

Install the package and its dependencies:

```bash
composer require openeuropa/oe_theme_content
```


## Companion sub-modules

* [OpenEuropa Content Call for tenders companion module](./modules/oe_theme_content_call_tenders/README.md)
* [OpenEuropa Content Corporate Entity Contact companion module](./modules/oe_theme_content_entity_contact/README.md)
* [OpenEuropa Content Corporate Entity Organisation companion module](./modules/oe_theme_content_entity_organisation/README.md)
* [OpenEuropa Content Corporate Entity Venue companion module](./modules/oe_theme_content_entity_venue/README.md)
* [OpenEuropa Content Event companion module](./modules/oe_theme_content_event/README.md)
* [OpenEuropa Content News companion module](./modules/oe_theme_content_news/README.md)
* [OpenEuropa Content Page companion module](./modules/oe_theme_content_page/README.md)
* [OpenEuropa Content Policy companion module](./modules/oe_theme_content_policy/README.md)
* [OpenEuropa Content Project companion module](./modules/oe_theme_content_project/README.md)
* [OpenEuropa Content Publication companion module](./modules/oe_theme_content_publication/README.md)

## Development

The OpenEuropa Theme project contains all the necessary code and tools for an effective development process,
meaning:

- All PHP development dependencies (Drupal core included) are required in [composer.json](composer.json)
- All Node.js development dependencies are required in [package.json](package.json)
- Project setup and installation can be easily handled thanks to the integration with the [Task Runner][4] project.
- All system requirements are containerized using [Docker Composer][5].
- Every change to the code base will be automatically tested using [Drone][17].

Make sure you read the [developer documentation](./docs/developer-documentation.md) before starting to use the theme
in your projects.

### Project setup

In order to download all required PHP code run:

```bash
composer install
```

This will build a fully functional Drupal site in the `./build` directory that can be used to develop and showcase the
theme.

Before setting up and installing the site make sure to customize default configuration values by copying [runner.yml.dist](runner.yml.dist)
to `./runner.yml` and override relevant properties.

To set up the project run:

```bash
./vendor/bin/run drupal:site-setup
```

This will:

- Symlink the theme in  `./build/modules/custom/oe_theme_content` so that it's available to the target site
- Setup Drush and Drupal's settings using values from `./runner.yml.dist`
- Setup PHPUnit and Behat configuration files using values from `./runner.yml.dist`

**Please note:** project files and directories are symlinked within the test site by using the
[OpenEuropa Task Runner's Drupal project symlink](https://github.com/openeuropa/task-runner-drupal-project-symlink) command.

If you add a new file or directory in the root of the project, you need to re-run `drupal:site-setup` in order to make
sure they are be correctly symlinked.

If you don't want to re-run a full site setup for that, you can simply run:

```
$ ./vendor/bin/run drupal:symlink-project
```

After a successful setup install the site by running:

```bash
./vendor/bin/run drupal:site-install
```

This will:

- Install the target site
- Set the OpenEuropa Theme as the default theme
- Enable OpenEuropa Theme Demo and [Configuration development][6] modules

### Using Docker Compose

Alternatively, you can build a development site using [Docker](https://www.docker.com/get-docker) and
[Docker Compose](https://docs.docker.com/compose/) with the provided configuration.

Docker provides the necessary services and tools such as a web server and a database server to get the site running,
regardless of your local host configuration.

#### Requirements:

- [Docker](https://www.docker.com/get-docker)
- [Docker Compose](https://docs.docker.com/compose/)

#### Configuration

By default, Docker Compose reads two files, a `docker-compose.yml` and an optional `docker-compose.override.yml` file.
By convention, the `docker-compose.yml` contains your base configuration and it's provided by default.
The override file, as its name implies, can contain configuration overrides for existing services or entirely new
services.
If a service is defined in both files, Docker Compose merges the configurations.

Find more information on Docker Compose extension mechanism on [the official Docker Compose documentation](https://docs.docker.com/compose/extends/).

#### Usage

To start, run:

```bash
docker-compose up
```

It's advised to not daemonize `docker-compose` so you can turn it off (`CTRL+C`) quickly when you're done working.
However, if you'd like to daemonize it, you have to add the flag `-d`:

```bash
docker-compose up -d
```

Then:

```bash
docker-compose exec web composer install
docker-compose exec web ./vendor/bin/run drupal:site-install
```

Using default configuration, the development site files should be available in the `build` directory and the development site
should be available at: [http://127.0.0.1:8080/build](http://127.0.0.1:8080/build).

#### Running the tests

To run the grumphp checks:

```bash
docker-compose exec web ./vendor/bin/grumphp run
```

To run the phpunit tests:

```bash
docker-compose exec web ./vendor/bin/phpunit
```

To run the behat tests:

```bash
docker-compose exec web ./vendor/bin/behat
```

### Step debugging

To enable step debugging from the command line, pass the `XDEBUG_SESSION` environment variable with any value to
the container:

```bash
docker-compose exec -e XDEBUG_SESSION=1 web <your command>
```

Please note that, starting from XDebug 3, a connection error message will be outputted in the console if the variable is
set but your client is not listening for debugging connections. The error message will cause false negatives for PHPUnit
tests.

To initiate step debugging from the browser, set the correct cookie using a browser extension or a bookmarklet
like the ones generated at https://www.jetbrains.com/phpstorm/marklets/.

### Disable Drupal 8 caching

Manually disabling Drupal 8 caching is a laborious process that is well described [here][14].

Alternatively, you can use the following Drupal Console command to disable/enable Drupal 8 caching:

```bash
./vendor/bin/drupal site:mode dev  # Disable all caches.
./vendor/bin/drupal site:mode prod # Enable all caches.
```

Note: to fully disable Twig caching the following additional manual steps are required:

1. Open `./build/sites/default/services.yml`
2. Set the following parameters:

```yaml
parameters:
  twig.config:
    debug: true
    auto_reload: true
    cache: false
 ```
3. Rebuild Drupal cache: `./vendor/bin/drush cr`

This is due to the following [Drupal Console issue][15].

## Contributing

Please read [the full documentation](https://github.com/openeuropa/openeuropa) for details on our code of conduct,
and the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the available versions, see the [tags on this repository](https://github.com/openeuropa/oe_theme/tags).

[1]: https://github.com/ec-europa/europa-component-library
[2]: https://www.drupal.org/docs/develop/using-composer/using-composer-to-manage-drupal-site-dependencies#managing-contributed
[3]: https://github.com/openeuropa/oe_theme/releases
[4]: https://github.com/openeuropa/task-runner
[5]: https://docs.docker.com/compose
[6]: https://www.drupal.org/project/config_devel
[7]: https://nodejs.org/en
[8]: https://www.drupal.org/project/drupal/issues/474684
[9]: https://www.drupal.org/files/issues/474684-151.patch
[10]: https://www.drupal.org/docs/8/extending-drupal-8/installing-themes
[11]: https://www.drupal.org/docs/8/theming-drupal-8/adding-stylesheets-css-and-javascript-js-to-a-drupal-8-theme
[12]: https://www.docker.com/get-docker
[13]: https://docs.docker.com/compose
[14]: https://www.drupal.org/node/2598914
[15]: https://github.com/hechoendrupal/drupal-console/issues/3854
[16]: https://github.com/openeuropa/ecl-twig-loader
[17]: https://drone.io
[18]: https://www.npmjs.com/package/patch-package
[19]: https://github.com/openeuropa/composer-artifacts
