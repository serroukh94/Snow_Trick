<?php

namespace App\Twig;

use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class DicebearExtension extends AbstractExtension
{
    const BASE_URL = "https://avatars.dicebear.com/api/avataaars/";

    public function getFunctions(): array
    {
        return [
            new TwigFunction('dicebear', [$this, 'dicebear'], ['is_safe' => ['html']]),
        ];
    }

    public function dicebear(User $user, int $size = 50)
    {
        return '<img 
            src="' . self::BASE_URL . $user->getId() . '.svg?size=' . $size . '" 
            alt="' . $user->getFullName() . '"
            title="' . $user->getFullName() . '">';
    }
}