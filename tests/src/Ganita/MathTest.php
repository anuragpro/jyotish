<?php
/**
 * @link      http://github.com/kunjara/jyotish for the canonical source repository
 * @license   GNU General Public License version 2 or later
 */

namespace JyotishTest\Ganita;

use Jyotish\Ganita\Math;
use Jyotish\Bhava\Bhava;

/**
 * @group Ganita
 */
class MathTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Jyotish\Ganita\Math::dmsToDecimal
     * @dataProvider providerDmsToDecimal
     */
    public function testDmsToDecimal($dms, $decimal)
    {
        $decimalActual = Math::dmsToDecimal($dms);
        $this->assertEquals($decimal, $decimalActual, '', .001);
    }
    
    public function providerDmsToDecimal()
    {
        return [
            [['d' => 13, 'm' => 20], 13.33333],
            [['d' => -13, 'm' => 20], -13.33333],
            [['d' => 26, 'm' => -80], -27.33333],
            [['d' => 26, 'm' => 0, 's' => 60], 26.01667],
            [['d' => -13, 'm' => -20, 's' => -60], -13.35],
        ];
    }
    
    /**
     * @covers Jyotish\Ganita\Math::decimalToDms
     * @dataProvider providerDecimalToDms
     */
    public function testDecimalToDms($decimal, $dms)
    {
        $dmsActual = Math::decimalToDms($decimal);
        $this->assertEquals($dms, $dmsActual);
    }
    
    public function providerDecimalToDms()
    {
        return [
            [13.3333333333333333, ['d' => 13, 'm' => 20]],
            [-13.3333333333333333, ['d' => -13, 'm' => 20]],
            [26.0166666666666667, ['d' => 26, 'm' => 1]],
            [-13.35, ['d' => -13, 'm' => 21]],
        ];
    }
    
    /**
     * @covers Jyotish\Ganita\Math::partsToUnits
     */
    public function testPartsToUnits()
    {
        $value = 32.4;
        $this->assertEquals(['units' => 2, 'parts' => 2.4], Math::partsToUnits($value));
        $this->assertEquals(['units' => 1, 'parts' => 2.4], Math::partsToUnits($value, 30, 'floor'));
        $this->assertEquals(['units' => 4, 'parts' => 2.4], Math::partsToUnits($value, 10));
    }

    /**
     * @covers Jyotish\Ganita\Math::distanceInCycle
     * @dataProvider providerDistanceInCycle
     */
    public function testDistanceInCycle($n1, $n2, $distance)
    {
        $distanceActual = Math::distanceInCycle($n1, $n2, 12);
        $this->assertEquals($distance, $distanceActual);
    }
    
    public function providerDistanceInCycle()
    {
        return [
            [1, 6, 6],
            [6, 1, 8],
            [8, 4, 9],
        ];
    }
    
    /**
     * @covers Jyotish\Ganita\Math::numberInCycle
     * @expectedException InvalidArgumentException 
     */
    public function testNumberInCycle()
    {
        for ($i = -13; $i <= 13; $i++){
            $numbersActual[] = Math::numberInCycle(1, $i);
        }
        $numbersExpected = [
            1, 
            2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 1, 
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12,
            1
        ];
        $this->assertEquals($numbersExpected, $numbersActual);
    }
    
    /**
     * @covers Jyotish\Ganita\Math::numberNext
     * @depends testNumberInCycle
     */
    public function testNumberNext()
    {
        for($i = 1; $i <= 14; $i++){
            $numbersActual[] = Math::numberNext($i);
        }
        $numbersExpected = [
            2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3
        ];
        $this->assertEquals($numbersExpected, $numbersActual);
    }
    
    /**
     * @covers Jyotish\Ganita\Math::numberPrev
     * @depends testNumberInCycle
     */
    public function testNumberPrev()
    {
        for($i = 1; $i <= 14; $i++){
            $numbersActual[] = Math::numberPrev($i);
        }
        $numbersExpected = [
            12, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 1
        ];
        $this->assertEquals($numbersExpected, $numbersActual);
    }
    
    /**
     * @covers Jyotish\Ganita\Math::sign
     */
    public function testSign()
    {
        $value = -32.2;
        $this->assertEquals(-1, Math::sign($value));
        
        $value = .0;
        $this->assertEquals(0, Math::sign($value));
        
        $value = 722;
        $this->assertEquals(1, Math::sign($value));
    }
    
    /**
     * @covers Jyotish\Ganita\Math::arraySum
     */
    public function testArraySum()
    {
        $array1 = [
            'first' => -2,
            'second' => 23.6,
        ];
        $array2 = [
            8.9,
            'second' => 4,
            'first' => 3.3,
        ];
        $this->assertEquals([8.9, 'first' => 1.3, 'second' => 27.6], Math::arraySum($array1, $array2));
    }
    
    /**
     * @covers Jyotish\Ganita\Math::arrayInArray
     */
    public function testArrayInArray()
    {
        $array1 = [3, 7];
        $array2 = Bhava::$bhavaTrishadaya;
        $this->assertTrue(Math::arrayInArray($array1, $array2));
        $this->assertNotTrue(Math::arrayInArray($array1, $array2, true));
        
        $array1 = [1, 3, 5];
        $array2 = Bhava::$bhavaDusthana;
        $this->assertNotTrue(Math::arrayInArray($array1, $array2));
        $this->assertNotTrue(Math::arrayInArray($array1, $array2, true));
    }

    /**
     * @covers Jyotish\Ganita\Math::inRange
     * @dataProvider providerInRange
     */
    public function testInRange($value, $min, $max)
    {
        $this->assertTrue(Math::inRange($value, $min, $max));
    }
    
    public function providerInRange()
    {
        return [
            [2, 2, 3],
            [1, 0, 2],
            [1, -2, 2],
        ];
    }
    
    /**
     * @covers Jyotish\Ganita\Math::oppositeValue
     */
    public function testOppositeValue()
    {
        $value = 11;
        $this->assertEquals(5, Math::oppositeValue($value));
        
        $value = 340.23;
        $this->assertEquals(160.23, Math::oppositeValue($value, 360));
    }

    /**
     * @covers Jyotish\Ganita\Math::simplifyNumber
     * @dataProvider providerSimplifyNumber
     */
    public function testSimplifyNumber($number, $num)
    {
        $numActual = Math::simplifyNumber($number);
        $this->assertEquals($num, $numActual);
    }
    
    public function providerSimplifyNumber()
    {
        return [
            [3, 3],
            [10, 1],
            [28, 1],
            [288, 9],
            [98765, 8]
        ];
    }
}
