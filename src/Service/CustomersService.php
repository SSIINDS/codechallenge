<?php
namespace App\Service;

use App\Entity\Customers;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CustomersService
{

    private $customers;
    public function __construct(
        private HttpClientInterface $client,
        private Customers $Customers,
        private ManagerRegistry $doctrine,
        private EntityManagerInterface $entityManager
    ) {

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
                $customer->setPassword($user['login']['password']);
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
    public function read(): array
    {
        
    }
    public function list(): array
    {
        
    }
}
