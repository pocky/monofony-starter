# Monofony Starter

A very opinionated monofony starter kit with a very opinionated DDD\Clean\Hexagonal architecture.

## Why?

I love writing code but I also need to bootstrap projects. This is why I am using
this starter kit with a lot of opinionated code. The main idea is to generate a lot
of classes in an architecture that is easy to understand, use, extend and replace.

I am using Monofony and ApiPlatform because they use Sylius and Symfony frameworks. 
Monofony is my backend user interface, ApiPlatform my API user interface.

## Installation

1. Clone this project 
2. Take a look at `configure` script. For example, if you want to setup a full docker environement for dev, 
run `./configure --env=dev --with-docker=full --with-proxy --clean-before-tasks`
3. Run `make install` to install the project
4. You should have a blank project for http://www.monofony.localhost with admin
http://www.monofony.localhost/admin and api http://www.monofony.localhost/api
5. Run the following commands to install the assets and the database (use `docker compose exec php` if you are using docker):
    - `$ bin/console app:install            # install the application with non-interactive mode`
    - `$ bin/console doctrine:fixtures:load # load data fixtures`
6. Profit!

## Usage

Follow the monofony documentation : https://docs.monofony.com/current/resources

## Usage of Maker commands

There is a lot of maker commands available. If you want to follow my architecture, use this:
Commands will always (at least) asks for a name and a package location. We will use `Blog` as context and `Post` as entity.

### Generate Domain and Infrastructure

We need to generate our domain and our entities. We don't need a lot of things:

- A package with apropriate configuration
- A domain identifier
- A custom generator for our identifier
- An entity
- A repository
- A factory for our entity
- A form

Everything will be located in a bounded context and shared for Symfony, Doctrine,
Sylius and ApiPlatform.

#### Generate Package with configuration

We want to crate our package and his configuration. We need to update 3 files and create a new package configuration.

```bash
bin/console make:package
```

#### Generate Identifier

We don't work with an auto-increment identifier because we want a total control on our identifier.

```bash
bin/console make:domain:identifier
```

Package should be something like `Blog`.

Two class will be generated:
- The identifier, located in `Blog\Shared\Domain\Identifier`
- The generator, located in `Blog\Shared\Infrastructure\Identity`

#### Generate Entity

We need to create our doctrine entity. This is the same as default EntityMaker
with package location and Sylius support.

We need doctrine entities because Sylius and ApiPlatform will use them.

```bash
bin/console make:infrastructure:persistence:entity Post -a -s
```

#### Generate Sylius Factory

We have a custom identifier, we need a dedicated sylius factory.

```bash
bin/console make:infrastructure:sylius:factory
```

Use your package location and the name of your identifier. The factory
will be located in `Blog\Shared\Infrastructure\Sylius\Factory`.

### Generate User Interface

We got two user interfaces:
- Sylius for our backend
- ApiPlatform for our API.

We need to create grid and form for Sylius. Sylius is our backend, that's why
I'm using namespace `Backend\Blog`.

#### Generate Sylius Grid

Last thing, we need to generate our Sylius Grid.

```bash
bin/console make:ui:sylius:grid
```

Package should be something like `Backend\Blog` and grid will be
located in `UI\Backend\Blog\Sylius\Grid`.

#### Generate Doctrine form

Same as default EntityFormMaker with package location.

```bash
bin/console make:ui:doctrine:form
```

Package should be something like `Backend\Blog` and grid will be
located in `UI\Backend\Blog\Sylius\Grid`.

### Configuration

Don't forget to add your entites to `config/packages/api_platform.php`, `config/packages/doctrine.php`
and `config/sylius/resources.php`. (Blog is our context)

#### ApiPlatform
```php
'mapping' => [
    'paths' => [
        '%kernel.project_dir%/src/Blog/Shared/Infrastructure/Persistence/Doctrine/ORM/Entity',
    ],
],
```

#### Doctrine
```php
'Blog' => [
    'is_bundle' => false,
    'type' => 'attribute',
    'dir' => '%kernel.project_dir%/src/Blog/Shared/Infrastructure/Persistence/Doctrine/ORM/Entity',
    'prefix' => 'App\Blog\Shared\Infrastructure\Persistence\Doctrine\ORM\Entity',
    'alias' => 'Blog',
],
```

#### Sylius
```php
'mapping' => [
    'paths' => [
        '%kernel.project_dir%/src/Blog/Shared/Infrastructure/Persistence/Doctrine/ORM/Entity',
    ],
],
'resources' => [
    'foo.bar' => [
        'classes' => [
            'model' => Post::class,
            'repository' => PostRepository::class,
            'form' => PostType::class,
            'factory' => PostFactory::class,
        ]
    ],
],
```

## Contributing

See the [CONTRIBUTING](.github/CONTRIBUTING.md) file.

## Code of conduct

Be nice and take a look on our [CODE OF CONDUCT](.github/CODE_OF_CONDUCT.md).

## Support

This project is open source and this is our [support rules](.github/SUPPORT.md).

## License

This project is licensed under MIT.

## Credits

Created by [Alexandre Balmes](https://alexandre.balmes.co).
See also the [thank you](.github/thank-you.md).
