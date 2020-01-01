<?php

namespace Pussle\ORM\SQL;

use Pussle\ORM\SQL\Where;

interface Command
{
    public function setWhere(Where $where);
    public function getWhere();
    public function build();
}