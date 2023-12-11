<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;
use App\Entity\Post;

#[AsCommand(
    name: 'app:fetch-posts',
    description: 'Fetch posts from API and save them to the database',
)]
class FethPostCommand extends Command
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this->setName('app:fetch-posts')
            ->setDescription('Fetch posts from API and save them to the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();

        $response = $httpClient->request('GET', 'https://jsonplaceholder.typicode.com/posts');
        $postsData = $response->toArray();

        $userResponse = $httpClient->request('GET', 'https://jsonplaceholder.typicode.com/users/');
        $usersData = $userResponse->toArray();

        foreach ($postsData as $postData) {          

            $post = new Post();
            $post->setTitle($postData['title']);
            $post->setUserId($postData['userId']);
            $post->setId($postData['id']);
            $post->setBody($postData['body']);          
        

            $this->entityManager->persist($post);
        }

        foreach ($usersData as $userData) {
           
             $user = new Users();
             $user->setName($userData['name']);
             $user->setUsername($userData['username']);
             $user->setEmail($userData['email']);       
        
            $this->entityManager->persist($user);
           
        }

        $this->entityManager->flush();

        $output->writeln('Posts successfully fetched and saved to the database.');

        return Command::SUCCESS;
    }
}
