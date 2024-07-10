<?php
namespace App\Service;

use App\Entity\Customers;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CustomersService
{

    protected $customers;
    protected $client; 
    protected $managerRegistry; 
    protected $entityManager; 
    protected $userPasswordHasher; 

    public function __construct(
        HttpClientInterface $HttpClientInterface,
        Customers $Customers,
        ManagerRegistry $ManagerRegistry,
        EntityManagerInterface $EntityManagerInterface,
        UserPasswordHasherInterface $UserPasswordHasherInterface
    ) {
        $this->client = $HttpClientInterface;
        $this->managerRegistry = $ManagerRegistry;
        $this->entityManager = $EntityManagerInterface;
        $this->userPasswordHasher = $UserPasswordHasherInterface;
        $this->customers = $this->entityManager->getRepository($Customers::class);
    }

    public function getRandomUsers($count)
    {
        $response = $this->client->request(
            'GET',
            "https://randomuser.me/api/?nat=au&results=$count"
        );

        $statusCode = $response->getStatusCode();
        if($statusCode != 200){
            return false;
        }

        $content = $response->getContent();
        $content = $response->toArray();
        return $content['results'];
    }

    public function fetch($count = 10): array
    {
        
        $users = $this->getRandomUsers($count);
        if(!$users){
            return [
                "status"    => false,
                "message"   => "API Error Response"
            ];
        }
        $em = $this->managerRegistry->getManager();
        $em->getConnection()->beginTransaction();
        $em->getConnection()->setAutoCommit(false);
        try {
            foreach ($users as $key => $user) {
                if(
                    !array_key_exists("email", $user) ||
                    !array_key_exists("name", $user) ||
                    !array_key_exists("login", $user) ||
                    !array_key_exists("gender", $user) ||
                    !array_key_exists("location", $user) ||
                    !array_key_exists("phone", $user) 
                ){
                    return [
                        "status"    => false,
                        "message"   => "Invalid RandomUser API Response"
                    ];
                }
                $entity = $this->customers->findOneBy(["Email" => $user['email']]);
                $customer = $entity ? $entity : new Customers();
                $customer->setFirstName($user['name']['first']);
                $customer->setLastName($user['name']['last']);
                $customer->setEmail($user['email']);
                $customer->setUsername($user['login']['username']);
                $customer->setPassword(
                    $this->userPasswordHasher->hashPassword(
                        $customer,
                        $user['login']['password']
                    )
                );
                $customer->setGender($user['gender']);
                $customer->setCountry($user['location']['country']);
                $customer->setCity($user['location']['city']);
                $customer->setPhone($user['phone']);
                $entity ? $customer->setUpdateDate() : $customer->setCreateDate();

                $em->persist($customer);
                $em->flush();
                $em->getConnection()->commit();

            }
        } catch (Exception $e) {
            $em->getConnection()->rollBack();
            return [
                "status"    => false,
                "message"   => "Error fetching API data."
            ];
        }

        return [
            "status"    => true,
            "message"   => "$count Customers has been successfully saved."
        ];
    }

    public function read($id)
    {
        return $this->customers->getCustomers($id);
    }
    
    public function list()
    {
        return $this->customers->getCustomers();
    }
}
