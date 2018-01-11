<?php

namespace Entities;

class User
{
    public $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}
