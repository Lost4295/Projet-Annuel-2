<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HasroleExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('hasrole', [$this, 'HasRole']),
        ];
    }

    public function HasRole(string $role, User $user): int
    {
        return $user->hasRole($role);
    }
}