<?php

use PHPUnit\Framework\TestCase;

require_once("../functions.php");



final class functionTest extends TestCase{

    public function testConvertMark(){
        //wrong parameter, expected -1
        $result=convertMark("wrong");
        $this->assertSame($result, -1);

        //number not in [0-10], expected -1
        $result=convertMark("12");
        $this->assertSame($result, -1);
        $result=convertMark("-2");
        $this->assertSame($result, -1);
        $result=convertMark("10/11");
        $this->assertSame($result, -1);

        //number not in correct form, expected -1
        $result=convertMark("2/1");
        $this->assertSame($result, -1);
        $result=convertMark("+5");
        $this->assertSame($result, -1);
        $result=convertMark("7,5");
        $this->assertSame($result, -1);

        //number with /, expected num.75
        $result=convertMark("6/7");
        $this->assertSame($result, 6.75);

        //number with +, expected num.25
        $result=convertMark("1+");
        $this->assertSame($result, 1.25);

        //number with -, expected num-1.75
        $result=convertMark("5-");
        $this->assertSame($result, 4.75);

        //number with .5, expected num.5
        $result=convertMark("5.5");
        $this->assertSame($result, 5.5);

        //int number, expected num.0
        $result=convertMark("7");
        $this->assertSame($result, 7.0);
    }
}