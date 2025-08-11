<?php

namespace Unit;
include 'assets/controller/methods.php';

use PHPUnit\Framework\TestCase;

class methods_test extends TestCase
{
    public function testValidate()
    {
        $testCases = [
            "  Hello World  " => "Hello World",
            "<script>alert('XSS')</script>" => "&lt;script&gt;alert(&#039;XSS&#039;)&lt;/script&gt;",
            "O'Reilly\\Books" => "O&#039;ReillyBooks",
            "   &copy; 2025   " => "&amp;copy; 2025",
            "NormalText" => "NormalText",
        ];


        foreach ($testCases as $input => $expected) {
            $this->assertEquals($expected, validate($input), "Failed asserting that validate('$input') equals '$expected'");
        }
    }

    public function testCheckUsernameReturnsTrueWhenExists()
    {
        $existingUsername = 'yshabanei@gmail.com'; // فرض کن این وجود داره

        $result = checkUsername($existingUsername);
        $this->assertTrue($result);
    }

    public function testCheckUsernameReturnsFalseWhenNotExists()
    {
        $nonExistingUsername = 'not_existing_user_123456@example.com'; // یه چیزی که مطمئناً وجود نداره

        $result = checkUsername($nonExistingUsername);
        $this->assertFalse($result);
    }


}
