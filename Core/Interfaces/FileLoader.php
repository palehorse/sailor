<?php
namespace Sailor\Core\Interfaces;

Interface FileLoader extends Loader
{
    public function load(Loaded $Loaded);
}