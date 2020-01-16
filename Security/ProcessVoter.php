<?php


namespace Kontrolgruppen\CoreBundle\Security;


use Kontrolgruppen\CoreBundle\Entity\Process;
use Kontrolgruppen\CoreBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ProcessVoter extends Voter
{
    // these strings are just invented: you can use anything
    const VIEW = 'view';
    const EDIT = 'edit';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Process) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
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

        // you know $subject is a Process object, thanks to supports
        /** @var Process $process */
        $process = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($process, $user);
            case self::EDIT:
                return $this->canEdit($process, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Process $process, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($process, $user)) {
            return true;
        }

        if ($this->security->isGranted('ROLE_EXTERNAL')) {
            return true;
        }

        return false;
    }

    private function canEdit(Process $post, User $user)
    {
        return $this->security->isGranted('ROLE_SAGSBEHANDLER');
    }
}
