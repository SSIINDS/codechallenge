# Flexisource Code Challenge

## Getting Started
1. Clone the project by running `git clone https://gitlab.com/mrkong.villanueva/flexisource-code-challenge.git` 
1. Go to `flexisource-code-challenge` folder and run `composer install` to install dependencies.
1. Run `docker-compose up` to start MySQL. (make sure you have docker installed in your local machine)
1. Run `php bin/console doctrine:migrations:migrate` to run the data migration.
1. Run `symfony serve` to run the project.

## Fetching Data from API from CLI
#### This command function will fetch 100 users from https://randomuser.me
`php bin/console cron:fetch-customers`

#### Adding `count` as an option to define the number of users to be fetch from API
`php bin/console cron:fetch-customers --count=10`
