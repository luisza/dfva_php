<?php
use PHPUnit\Framework\TestCase;
include(dirname(__FILE__).'/../settings.php');

class SettingsTest extends TestCase
{
    public function testSettingsData(){
        $website_regex = "/^(http\:\/\/|https\:\/\/)?([a-z0-9][a-z0-9\-]*\.)+[a-z0-9][a-z0-9\-]*$/";
        echo Settings::getDfvaServerUrl();
        $this->assertTrue(preg_match($website_regex, Settings::getDfvaServerUrl()) == TRUE);
    }
}
#TODO eliminate StackTest later on (It is being used to test phpunit
#and have some sample code on how to make the code for phpunit
class StackTest extends TestCase
{
    public function testPushAndPop()
    {
        $stack = [];
        $this->assertSame(0, count($stack));

        array_push($stack, 'foo');
        $this->assertSame('foo', $stack[count($stack)-1]);
        $this->assertSame(1, count($stack));

        $this->assertSame('foo', array_pop($stack));
        $this->assertSame(0, count($stack));
    }
}
