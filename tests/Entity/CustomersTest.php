<?php

namespace App\Tests\Entity;

use App\Entity\Customers;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class CustomersTest extends TestCase
{
    
    public function testCustomerCreate(): void
    {

        $customer = new Customers();
        $customer->setFirstName("Juan");
        $customer->setLastName("Dela Cruz");
        $customer->setEmail("jdc@yopmail.com");
        $customer->setUsername("jdelacruz");
        $customer->setPassword("testpasswordmd5");
        $customer->setGender("male");
        $customer->setCountry("Australia");
        $customer->setCity("Victoria");
        $customer->setPhone("04-1502-6922");
        $customer->setUpdateDate();
        $customer->setCreateDate();

        $this->assertEquals("Juan", $customer->getFirstName());
        $this->assertEquals("Dela Cruz", $customer->getLastName());
        $this->assertEquals("Juan Dela Cruz", $customer->getFullName());
        $this->assertEquals("jdc@yopmail.com", $customer->getEmail());
        $this->assertEquals("jdelacruz", $customer->getUsername());
        $this->assertEquals("testpasswordmd5", $customer->getPassword());
        $this->assertEquals("male", $customer->getGender());
        $this->assertEquals("Australia", $customer->getCountry());
        $this->assertEquals("Victoria", $customer->getCity());
        $this->assertEquals("04-1502-6922", $customer->getPhone());

    }
}
