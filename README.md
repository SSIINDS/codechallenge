# Flexisource Code Challenge

## Getting Started
1. Clone the project by running `git clone https://github.com/SSIINDS/codechallenge.git` 
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
[https://127.0.0.1:8000/customers](https://127.0.0.1:8000/customers)

#### Sample Response
```json
{
  "status": true,
  "data": [
    {
      "id": 1,
      "FullName": "Cameron Morrison",
      "Email": "cameron.morrison@example.com",
      "Country": "Australia"
    },
    {
      "id": 2,
      "FullName": "Fred Weaver",
      "Email": "fred.weaver@example.com",
      "Country": "Australia"
    },
    {
      "id": 3,
      "FullName": "Jennie Hernandez",
      "Email": "jennie.hernandez@example.com",
      "Country": "Australia"
    },
    .......
    {
      "id": 100,
      "FullName": "Jim Mccoy",
      "Email": "jim.mccoy@example.com",
      "Country": "Australia"
    }
  ],
  "message": ""
}
```

## Getting Customer Details
[https://127.0.0.1:8000/customers/1](https://127.0.0.1:8000/customers/1)

#### Sample Response
```json
{
  "status": true,
  "data": {
    "id": 1,
    "FullName": "Cameron Morrison",
    "Email": "cameron.morrison@example.com",
    "Country": "Australia",
    "Username": "heavykoala839",
    "Gender": "male",
    "City": "Rockhampton",
    "Phone": "03-4557-1793"
  },
  "message": ""
}
```

## Run PHPUnit Test
`php bin/phpunit`