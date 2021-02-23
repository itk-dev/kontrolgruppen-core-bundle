<?php

namespace Kontrolgruppen\CoreBundle\Security;

use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ProcessEconomyVoter extends Voter
{
    // these strings are just invented: you can use anything
    const EDIT = 'edit';

    private $security;

    /**
     * ProcessEconomyVoter constructor.
     *
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!\in_array($attribute, [self::EDIT])) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Process) {
            return false;
        }

        // If last net collective sum is not null,
        // the process has been completed with revenue,
        // and therefore this voter should vote.
        return null !== $subject->getLastNetCollectiveSum();
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // ADMIN can do anything! The power!
        if ($this->security->isGranted(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN'])) {
            return true;
        }

        return false;
    }
}
