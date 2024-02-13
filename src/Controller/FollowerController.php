<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class FollowerController extends AbstractController
{
    #[Route('/follow/{id}', name: 'app_follow')]
    public function follow(
        User $userToFollow,
        EntityManagerInterface $entityManager,
        ManagerRegistry $doctrine,
        Request $request
    ): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if($userToFollow->getId() != $currentUser->getId()){
            $currentUser->follow($userToFollow);
            $doctrine->getManager()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/unfollow/{id}', name: 'app_unfollow')]
    public function unfollow(
        User $userToUnfollow,
        EntityManagerInterface $entityManager,
        ManagerRegistry $doctrine,
        Request $request
    ): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if($userToUnfollow->getId() != $currentUser->getId()){
            $currentUser->unfollow($userToUnfollow);
            $doctrine->getManager()->flush();
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
