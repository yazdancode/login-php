<?php

use PHPUnit\Framework\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    public function testFirstName()
    {
        $user = new User();
        $user->setFirstName('mehrdad');
        $this->assertEquals('mehrdad', $user->getFirstName());
    }

    public function testLastName()
    {
        $user = new User();
        $user->setLastName('sami');
        $this->assertEquals('sami', $user->getLastName());
    }

    public function testFullName()
    {
        $user = new User();
        $user->setFirstName('mehrdad');
        $user->setLastName('sami');
        $this->assertEquals('mehrdad sami', $user->getFullName());
    }
}
