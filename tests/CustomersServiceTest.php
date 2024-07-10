<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Entity\Customers;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\CustomersService;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CustomersServiceTest extends TestCase
{

    protected $managerRegistry;
    protected $entityManagerInterface;
    protected $customers;
    protected $passwordEncoderInterface;
    protected $httpClient;
    protected $mockResponse;
    protected $connection;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->entityManagerInterface = $this->createMock(EntityManagerInterface::class);
        $this->customers = $this->createMock(Customers::class);
        $this->passwordEncoderInterface = $this->createMock(UserPasswordHasherInterface::class);

        $this->entityManagerInterface->method('getConnection')->willReturn($this->connection);
        $this->managerRegistry->method('getManager')->willReturn($this->entityManagerInterface);

        $this->mockResponse = <<<JSON
            {
                "results": [
                    {
                    "gender": "female",
                    "name": {
                        "title": "Miss",
                        "first": "Erin",
                        "last": "Bowman"
                    },
                    "location": {
                        "street": {
                        "number": 7611,
                        "name": "Hunters Creek Dr"
                        },
                        "city": "Cairns",
                        "state": "Victoria",
                        "country": "Australia",
                        "postcode": 1688,
                        "coordinates": {
                        "latitude": "-17.7657",
                        "longitude": "133.2620"
                        },
                        "timezone": {
                        "offset": "-1:00",
                        "description": "Azores, Cape Verde Islands"
                        }
                    },
                    "email": "erin.bowman@example.com",
                    "login": {
                        "uuid": "5136c64d-612e-44c9-af14-77fdc8d9b700",
                        "username": "crazykoala542",
                        "password": "amber",
                        "salt": "fi6tXKk9",
                        "md5": "8174c7b80fdfce4b94a4628630d686b1",
                        "sha1": "aa65796742daf42d3c88cce2ed018da6c78cc3de",
                        "sha256": "f631ec3452efc5d423e7019098da1aa13d39b5a59ce6f2d5f7c39e08cc795d99"
                    },
                    "dob": {
                        "date": "1999-01-23T21:10:57.161Z",
                        "age": 25
                    },
                    "registered": {
                        "date": "2018-09-20T22:41:57.365Z",
                        "age": 5
                    },
                    "phone": "05-8574-0094",
                    "cell": "0437-192-043",
                    "id": {
                        "name": "TFN",
                        "value": "417162093"
                    },
                    "picture": {
                        "large": "https://randomuser.me/api/portraits/women/77.jpg",
                        "medium": "https://randomuser.me/api/portraits/med/women/77.jpg",
                        "thumbnail": "https://randomuser.me/api/portraits/thumb/women/77.jpg"
                    },
                    "nat": "AU"
                    }
                ],
                "info": {
                    "seed": "7569b4ff836b13c0",
                    "results": 1,
                    "page": 1,
                    "version": "1.4"
                }
            }
        JSON;
        $this->httpClient = new MockHttpClient([
            new MockResponse($this->mockResponse , ['http_code' => 200, 'response_headers' => ['Content-Type: application/json']])
        ]);

        parent::setUp();
    }

    public function testRandomUsersAPI(): void
    {
        $customersService = $this
            ->getMockBuilder(CustomersService::class)
            ->setConstructorArgs([
                $this->httpClient, 
                $this->customers, 
                $this->managerRegistry, 
                $this->entityManagerInterface, 
                $this->passwordEncoderInterface
            ])            
            ->onlyMethods([])
            ->getMock();

        $response = $customersService->getRandomUsers(1);
        $this->assertCount(1, $response); 
        $this->assertArrayHasKey('email', $response[0]);
        $this->assertArrayHasKey('name', $response[0]);
        $this->assertArrayHasKey('login', $response[0]);
        $this->assertArrayHasKey('gender', $response[0]);
        $this->assertArrayHasKey('location', $response[0]);
        $this->assertArrayHasKey('phone', $response[0]);
    }

    public function testFetchDataReturnNull(): void
    {

        $customersService = $this
            ->getMockBuilder(CustomersService::class)
            ->setConstructorArgs([
                $this->httpClient, 
                $this->customers, 
                $this->managerRegistry, 
                $this->entityManagerInterface, 
                $this->passwordEncoderInterface
            ])
            ->setMethods(['getRandomUsers'])
            ->getMock();
        
        $customersService->expects($this->any())
            ->method("getRandomUsers")
            ->willReturn(null);
                

        $result = $customersService->fetch();
        $this->assertFalse($result['status']); 
        $this->assertSame("API Error Response", $result['message']); 
    }

    public function testFetchDataReturnSuccess(): void
    {

        $customersService = $this
            ->getMockBuilder(CustomersService::class)
            ->setConstructorArgs([
                $this->httpClient, 
                $this->customers, 
                $this->managerRegistry, 
                $this->entityManagerInterface, 
                $this->passwordEncoderInterface
            ])
            ->setMethods(['getRandomUsers','managerRegistry'])
            ->getMock();
        
        $response = json_decode($this->mockResponse, true);
        $customersService->expects($this->any())
            ->method("getRandomUsers")
            ->willReturn($response['results']);
        
        $result = $customersService->fetch();
        $this->assertTrue($result['status']); 
        $this->assertStringContainsString("Customers has been successfully saved.", $result['message']); 
    }

    public function testFetchDataReturnIncomplete(): void
    {

        $customersService = $this
            ->getMockBuilder(CustomersService::class)
            ->setConstructorArgs([
                $this->httpClient, 
                $this->customers, 
                $this->managerRegistry, 
                $this->entityManagerInterface, 
                $this->passwordEncoderInterface
            ])
            ->setMethods(['getRandomUsers','managerRegistry'])
            ->getMock();
        
        $response = json_decode($this->mockResponse, true);
        unset($response['results'][0]['email']);
        unset($response['results'][0]['name']);
        unset($response['results'][0]['login']);
        unset($response['results'][0]['gender']);
        unset($response['results'][0]['phone']);
        unset($response['results'][0]['location']);

        $customersService->expects($this->any())
            ->method("getRandomUsers")
            ->willReturn($response['results']);
        
        $result = $customersService->fetch();
        $this->assertFalse($result['status']); 
        $this->assertStringContainsString("Invalid RandomUser API Response", $result['message']); 
    }

}
