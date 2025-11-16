<?php
namespace App\Security;

use App\Entity\Course;
use App\Entity\User;
use App\Repository\EnrollmentRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CourseAccessVoter extends Voter
{
    public const VIEW = 'COURSE_VIEW';

    public function __construct(private EnrollmentRepository $enrollments) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::VIEW && $subject instanceof Course;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User|null $user */
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false; // pas connecté
        }

        // Bypass pour les admins
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        /** @var Course $course */
        $course = $subject;

        // (optionnel) exiger un compte vérifié :
        if (!$user->isVerified()) {
            return false;
        }

        // le cœur: a-t-il acheté ce cours ?
        return $this->enrollments->hasAccess($user, $course);
    }
}
