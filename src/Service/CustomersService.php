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
    protected $doctrine; 
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
        $this->doctrine = $ManagerRegistry;
        $this->entityManager = $EntityManagerInterface;
        $this->userPasswordHasher = $UserPasswordHasherInterface;
        $this->customers = $this->entityManager->getRepository($Customers::class);
    }

    public function fetch($result = 10): array
    {
        $response = $this->client->request(
            'GET',
            "https://randomuser.me/api/?nat=au&results=$result"
        );

        $statusCode = $response->getStatusCode();
        if($statusCode != 200){
            return [
                "status"    => false,
                "message"   => "API Error Response"
            ];
        }

        $content = $response->getContent();
        $content = $response->toArray();
        $users = $content['results'];

        $em = $this->doctrine->getManager();
        $em->getConnection()->beginTransaction();
        $em->getConnection()->setAutoCommit(false);
        try {
            foreach ($users as $key => $user) {
                $customer = $this->customers->findOneBy(["Email" => $user['email']]) ?? new Customers;
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
                $this->customers->findOneBy(["Email" => $user['email']]) 
                ? $customer->setUpdateDate() 
                : $customer->setCreateDate();

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
            "message"   => "$result Customers has been successfully saved."
        ];
    }
    public function read($id): array
    {
        return $this->customers->getCustomers($id);
    }
    public function list()
    {
        return $this->customers->getCustomers();
    }
}
