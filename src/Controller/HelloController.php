<?php 

  namespace App\Controller;

  use App\Entity\User;
  use App\Entity\UserProfile;
  use Doctrine\ORM\EntityManagerInterface;
  use App\Repository\UserProfileRepository;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\Routing\Attribute\Route;
  use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

  class HelloController extends AbstractController {

    private array $messages = [
      ['message' => 'Hello', 'created' => '2024/06/12'],
      ['message' => 'Hi', 'created' => '2022/04/12'],
      ['message' => 'Bye', 'created' => '2022/05/12'],
    ];

    

    #[Route('/', name: 'app_index')]
    public function index(
      UserProfileRepository $profiles, 
      EntityManagerInterface $entityManager): Response 
    {  
      //$user = new User();
      //$user->setEmail('email@email.com'); 
      //$user->setPassword('12345678');

      //$profile = new UserProfile();
      //$profile->setUser($user);
      //$entityManager->persist($profile);
      //$entityManager->flush();

      //$profile = $profiles->find(1);
      //$entityManager->remove($profile);
      //$entityManager->flush();

      $limit = 2;

      return $this->render(
        'hello/index.html.twig',
        [
          'messages' => $this->messages,
          'limit' => $limit
        ]
      );
    }

    #[Route('/messages/{id<\d+>}', name: 'app_show_one')]
    public function showOne(int $id): Response {
      return $this->render(
        'hello/show_one.html.twig',
        [
          'message' => $this->messages[$id]
        ]
      );
    }

  }

?>