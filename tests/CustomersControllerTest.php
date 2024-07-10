<?php

namespace App\Tests;
use App\Service\CustomersService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomersControllerTest extends WebTestCase
{
    protected $client;
    protected $connection;
    protected $httpClient;
    protected $managerRegistry;
    protected $customersService;
    protected $entityManagerInterface;
    protected $passwordEncoderInterface;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->customersService = $this->createMock(CustomersService::class);

        parent::setUp();
    }

    public function testCustomersListCorrectResponseFormat(): void
    {

        $this->customersService->expects($this->once())->method("list")->willReturn([]);
        $this->client->getContainer()->set(CustomersService::class, $this->customersService);

        $this->client->request('GET', '/customers');
        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
        $this->assertEquals(
            200, $response->getStatusCode(),
            $response->getContent()
        );
        $content = json_decode($response->getContent());
        $this->assertObjectHasProperty('status', $content);
        $this->assertObjectHasProperty('data', $content);
        $this->assertObjectHasProperty('message', $content);
    
    }

    public function testCustomersListEmpty(): void
    {

        $this->customersService->expects($this->any())->method("list")->willReturn([]);
        $this->client->getContainer()->set(CustomersService::class, $this->customersService);

        $this->client->request('GET', '/customers');
        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
        $this->assertEquals(
            200, $response->getStatusCode(),
            $response->getContent()
        );
        $content = json_decode($response->getContent());
        $this->assertTrue($content->status);
        $this->assertEquals(0, count($content->data));
    
    }


    public function testCustomersListSuccess(): void
    {
        $this->customersService->expects($this->any())->method("list")->willReturn([
            [
                "id" => 1, 
                "FullName" => "Cameron Morrison", 
                "Email" => "cameron.morrison@example.com", 
                "Country" => "Australia" 
            ], 
            [
                "id" => 2, 
                "FullName" => "Fred Weaver", 
                "Email" => "fred.weaver@example.com", 
                "Country" => "Australia" 
            ] 
        ]);
        $this->client->getContainer()->set(CustomersService::class, $this->customersService);
        $this->client->request('GET', '/customers');
        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
        $this->assertEquals(
            200, $response->getStatusCode(),
            $response->getContent()
        );
        $content = json_decode($response->getContent());
        $this->assertTrue($content->status);
        $this->assertEquals(2, count($content->data));
    
    }

    public function testCustomerDetailsCorrectResponseFormat(): void
    {

        $this->customersService->expects($this->any())->method("read")->willReturn([
            "id" => 1, 
            "FullName" => "Cameron Morrison", 
            "Email" => "cameron.morrison@example.com", 
            "Country" => "Australia", 
            "Username" => "heavykoala839", 
            "Gender" => "male", 
            "City" => "Rockhampton", 
            "Phone" => "03-4557-1793" 
        ]);
        $this->client->getContainer()->set(CustomersService::class, $this->customersService);

        $this->client->request('GET', '/customers/1');
        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
        $this->assertEquals(
            200, $response->getStatusCode(),
            $response->getContent()
        );
        $content = json_decode($response->getContent());
        $this->assertObjectHasProperty('status', $content);
        $this->assertObjectHasProperty('data', $content);
        $this->assertObjectHasProperty('message', $content);

        $this->assertObjectHasProperty('id', $content->data);
        $this->assertObjectHasProperty('FullName', $content->data);
        $this->assertObjectHasProperty('Email', $content->data);
        $this->assertObjectHasProperty('Country', $content->data);
        $this->assertObjectHasProperty('Username', $content->data);
        $this->assertObjectHasProperty('Gender', $content->data);
        $this->assertObjectHasProperty('City', $content->data);
        $this->assertObjectHasProperty('Phone', $content->data);

        $this->assertSame(1, $content->data->id);
        $this->assertSame('Cameron Morrison', $content->data->FullName);
        $this->assertSame('cameron.morrison@example.com', $content->data->Email);
        $this->assertSame('Australia', $content->data->Country);
        $this->assertSame('heavykoala839', $content->data->Username);
        $this->assertSame('male', $content->data->Gender);
        $this->assertSame('Rockhampton', $content->data->City);
        $this->assertSame('03-4557-1793', $content->data->Phone);
    
    }
    
    public function testCustomerDetailsNotFound(): void
    {
        $this->customersService->expects($this->any())->method("read")->willReturn([]);
        $this->client->getContainer()->set(CustomersService::class, $this->customersService);

        $this->client->request('GET', '/customers/2');
        $response = $this->client->getResponse();

        $this->assertResponseIsSuccessful();
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
        $this->assertEquals(
            200, $response->getStatusCode(),
            $response->getContent()
        );
        $content = json_decode($response->getContent());
        $this->assertObjectHasProperty('status', $content);
        $this->assertObjectHasProperty('message', $content);

        $this->assertFalse($content->status);
        $this->assertEquals("Customer not found.", $content->message);
    
    }

}
