# Flexisource Code Challenge

## Getting Started
1. Clone the project by running `git clone https://gitlab.com/mrkong.villanueva/flexisource-code-challenge.git` 
1. Go to `flexisource-code-challenge` folder and run `composer install` to install dependencies.
1. Run `docker-compose up` to start MySQL. (make sure you have docker installed in your local machine)
1. Run `composer run migrate` to run the data migration.
1. Run `symfony serve` to run the project.

## Fetching Data from API using CLI
#### This command function will fetch 100 users from https://randomuser.me
`php bin/console cron:fetch-customers`

#### Adding `count` as an option to define the number of users to be fetch from API
`php bin/console cron:fetch-customers --count=10`

## Getting list of Customers
`https://127.0.0.1:8000/customers`

## Getting Customer Details
`https://127.0.0.1:8000/customers/1`
