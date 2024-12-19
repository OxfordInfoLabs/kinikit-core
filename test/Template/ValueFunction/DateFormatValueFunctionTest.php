<?php

namespace Kinikit\Core\Template\ValueFunction;

use PHPUnit\Framework\TestCase;

include_once "autoloader.php";

class DateFormatValueFunctionTest extends TestCase {

    public function testFunctionIsResolvedForKnownFunctionNames() {

        $function = new DateFormatValueFunction();
        $this->assertFalse($function->doesFunctionApply("imaginary"));
        $this->assertFalse($function->doesFunctionApply("test"));

        $this->assertTrue($function->doesFunctionApply("dateConvert 'd/m/y' 'Y-m-d'"));
        $this->assertTrue($function->doesFunctionApply("ensureDateFormat 'd/m/Y'"));
        $this->assertTrue($function->doesFunctionApply("now 'd/m/Y'"));
        $this->assertTrue($function->doesFunctionApply("dayOfMonth"));
        $this->assertTrue($function->doesFunctionApply("dayOfWeek"));
        $this->assertTrue($function->doesFunctionApply("dayName"));
        $this->assertTrue($function->doesFunctionApply("monthName"));
        $this->assertTrue($function->doesFunctionApply("month"));
        $this->assertTrue($function->doesFunctionApply("year"));
        $this->assertTrue($function->doesFunctionApply("date"));
        $this->assertTrue($function->doesFunctionApply("dateAdd"));
        $this->assertTrue($function->doesFunctionApply("dateSub"));
        $this->assertTrue($function->doesFunctionApply("formattedDuration"));

    }


    public function testCanEnsureDateFormat() {
        $function = new DateFormatValueFunction();
        $this->assertEquals('2020-01-01', $function->applyFunction("ensureDateFormat 'Y-m-d'", "2020-01-01", []));
        $this->assertNull($function->applyFunction("ensureDateFormat 'Y-m-d'", "01/01/2020", []));
    }

    public function testCanConvertDatesUsingDateConvert() {
        $function = new DateFormatValueFunction();
        $this->assertEquals('01/01/2020', $function->applyFunction("dateConvert 'Y-m-d' 'd/m/Y'", "2020-01-01", []));
        $this->assertEquals('01/01/2020 10:44:33', $function->applyFunction("dateConvert 'Y-m-d H:i:s' 'd/m/Y H:i:s'", "2020-01-01 10:44:33", []));
        $this->assertNull($function->applyFunction("dateConvert 'Y-m-d' 'd/m/Y'", "Invalid", []));
    }


    public function testCanGetDayOfMonthForWholeSQLDateOrDateTime() {

        $function = new DateFormatValueFunction();
        $this->assertEquals(25, $function->applyFunction("dayOfMonth", "2020-03-25", []));
        $this->assertEquals(25, $function->applyFunction("dayOfMonth", "2020-03-25 10:00:00", []));
        $this->assertEquals("05", $function->applyFunction("dayOfMonth", "2020-03-05 10:00:00", []));
    }

    public function testCanGetDayOfWeekForWholeSQLDateOrDateTime() {

        $function = new DateFormatValueFunction();
        $this->assertEquals(5, $function->applyFunction("dayOfWeek", "2021-12-02", []));
        $this->assertEquals(5, $function->applyFunction("dayOfWeek", "2021-12-02 10:00:00", []));

    }

    public function testCanGetDayNameForWholeSQLDateOrDateTime() {

        $function = new DateFormatValueFunction();
        $this->assertEquals("Thursday", $function->applyFunction("dayName", "2021-12-02", []));
        $this->assertEquals("Thursday", $function->applyFunction("dayName", "2021-12-02 10:00:00", []));

    }

    public function testCanGetMonthForWholeSQLDateOrDateTime() {

        $function = new DateFormatValueFunction();
        $this->assertEquals(12, $function->applyFunction("month", "2021-12-02", []));
        $this->assertEquals("01", $function->applyFunction("month", "2021-01-02 10:00:00", []));

    }

    public function testCanGetMonthNameForWholeSQLDateOrDateTimeOrInteger() {

        $function = new DateFormatValueFunction();
        $this->assertEquals("December", $function->applyFunction("monthName", "2021-12-02", []));
        $this->assertEquals("January", $function->applyFunction("monthName", "2021-01-02 10:00:00", []));
        $this->assertEquals("February", $function->applyFunction("monthName", 2, []));
    }

    public function testCanGetYearForWholeSQLDateOrDateTime() {

        $function = new DateFormatValueFunction();
        $this->assertEquals(2021, $function->applyFunction("year", "2021-12-02", []));

    }

    public function testCanConvertJSDateFormatIntoPHP() {

        $function = new DateFormatValueFunction();
        $this->assertEquals("2023-01-02 08:00:00", $function->applyFunction("date 'YYYY-MM-DD HH:mm:ss'", "08:00 2-1-2023", null));

    }

    public function testCanEvaluateAddAndASubtractDateExpressionsCorrectly() {

        $function = new DateFormatValueFunction();
        $this->assertEquals(date_create("08:00:00"), $function->applyFunction("dateAdd 'hours' 2", "06:00:00", null));
        $this->assertEquals(date_create("2023-02-01"), $function->applyFunction("dateAdd 'months' 1", "2023-01-01", null));
        $this->assertEquals(date_create("2023-02-15 10:00:00"), $function->applyFunction("dateAdd 'days' 14", "2023-02-01 10:00:00", null));

        $this->assertEquals(date_create("04:00:00"), $function->applyFunction("dateSub 'hours' 2", "06:00:00", null));
        $this->assertEquals(date_create("2023-02-01"), $function->applyFunction("dateSub 'months' 1", "2023-03-01", null));
        $this->assertEquals(date_create("2023-01-18 10:00:00"), $function->applyFunction("dateSub 'days' 14", "2023-02-01 10:00:00", null));
    }

    public function testCanFormatADurationCorrectly() {

        $function = new DateFormatValueFunction();
        $this->assertEquals("1 Day", $function->applyFunction("formattedDuration", 86400000, null));
        $this->assertEquals("1 Day", $function->applyFunction("formattedDuration seconds", 86400, null));
        $this->assertEquals("1 Minute", $function->applyFunction("formattedDuration milliseconds 1", 80123, null));
        $this->assertEquals("2 Days 4 Hours 3 Seconds", $function->applyFunction("formattedDuration", 187203000, null));

    }

    public function testCanFormatCurrentDateUsingNow(){
        $function = new DateFormatValueFunction();
        $this->assertEquals(date("Y-m-d"), $function->applyFunction("now 'Y-m-d'", "1", []));
        $this->assertEquals(date("Y-m-d H:i:s"), $function->applyFunction("now 'Y-m-d H:i:s'", "1", []));

    }

}