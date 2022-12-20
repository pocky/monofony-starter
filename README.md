# monofony

// todo

## Why?

// todo

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
