<?php
namespace Sailor\Extensions\Twig;

use Sailor\Utility\Session;
use \Twig\TwigFunction;
use \Twig\Extension\AbstractExtension;

class MemberExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('name', [$this, 'name']),
        ];
    }

    public function name()
    {
        if (!Session::has('member')) {
            return null;
        }

        $member = Session::get('member');
        return $member->name;
    }
}  