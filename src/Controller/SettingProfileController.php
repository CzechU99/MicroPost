<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\UserProfileType;
use App\Form\ProfileImageType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class SettingProfileController extends AbstractController
{
    #[Route('/setting/profile', name: 'app_setting_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profile(
        Request $request,
        UserRepository $users,
        EntityManagerInterface $entityManager
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $userProfile = $user->getUserProfile() ?? new UserProfile();

        $form = $this->createForm(
            UserProfileType::class,
            $userProfile
        );
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $userProfile = $form->getData();
            $user->setUserProfile($userProfile);
            $entityManager->persist($userProfile);
            $entityManager->flush();

            $this->addFlash('success', 'Profile updated');

            return $this->redirectToRoute('app_setting_profile');
        }

        return $this->render('setting_profile/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/setting/profile_image', name: 'app_setting_profile_image')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profileImage(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
    ): Response{
        $form = $this->createForm(ProfileImageType::class);

        /** @var User $user */
        $user = $this->getUser();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $profileImageFile = $form->get('profileImage')->getData();

            if($profileImageFile){
                $originalFileName = pathinfo($profileImageFile->getClientOriginalName(), PATHINFO_FILENAME);
            }
            $safeFileName = $slugger->slug($originalFileName);
            $newFileName = $safeFileName.'-'.uniqid().'.'.$profileImageFile->guessExtension();

            try{
                $profileImageFile->move(
                    $this->getParameter('profiles_directory'),
                    $newFileName
                );
            }catch (FileException $e){
            }

            $profile = $user->getUserProfile() ?? new UserProfile();
            $user->setUserProfile($profile);
            $profile->setImage($newFileName);
            $entityManager->persist($profile);
            $entityManager->flush();

            $this->addFlash('success', 'Profile image updated');

            return $this->redirectToRoute('app_setting_profile_image');
        }

        return $this->render('setting_profile/profile_image.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
