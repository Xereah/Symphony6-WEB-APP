<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;

class ProjectController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('app:fetch-posts')
            ->setDescription('Fetch posts from the API and save them to the database');
    }
  protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'https://jsonplaceholder.typicode.com/posts');
        $posts = $response->toArray();

        foreach ($posts as $postData) {
            // Fetch author information
            $authorResponse = $httpClient->request('GET', 'https://jsonplaceholder.typicode.com/users/' . $postData['userId']);
            $authorData = $authorResponse->toArray();

            // Create entities and persist to the database       
         

            $post = new Post();
            $post->setTitle($postData['title']);
            $post->setAuthor($user);
            $user->setName($authorData['name']);
            $user->setSurname($authorData['username']);

            $this->entityManager->persist($post);
        }

        $this->entityManager->flush();

        $output->writeln('Posts fetched and saved successfully.');

        return Command::SUCCESS;
    }
}
