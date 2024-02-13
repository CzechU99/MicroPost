<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class MicroPostVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\MicroPost;
    }

    /** 
     * @param MicroPost $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        $isAuth = $user instanceof UserInterface;

        if($this->security->isGranted('ROLE_ADMIN')){
            return true;
        }

        switch ($attribute) {
            case self::EDIT:
                return $isAuth 
                    && (
                        ($subject->getAuthor()->getId() === $user->getId()) ||
                        $this->security->isGranted('ROLE_EDITOR')
                    );
            case self::VIEW:
                if(!$subject->isExtraPrivacy()){
                    return true;
                }
                
                return $isAuth && 
                ($subject->getAuthor()->getId() === $user->getId()
                    || $subject->getAuthor()->getFollows()->contains($user)
                );
                
        }

        return false;
    }
}
