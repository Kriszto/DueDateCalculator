<?php


use DueDate\Calculator;
use PHPUnit\Framework\TestCase;
use DueDate\Exception\InvalidWorkingHourException;
use DueDate\Exception\InvalidWorkingDayException;
use DueDate\Exception\InvalidTurnaroundTimeException;

class DueDateCalculatorTest extends TestCase
{
    protected Calculator $dueDateCalculator;

    protected function setUp(): void
    {
        $this->dueDateCalculator = new DueDate\Calculator();
    }

    public function invalidWorkingDayProvider()
    {
        return [
            "Saturday" => [new DateTime('2021-02-06 12:00')],
            "Sunday" => [new DateTime('2021-02-07 12:00')],
        ];
    }

    /**
     * @dataProvider invalidWorkingDayProvider
     */
    public function testInvalidWorkday(DateTime $submitDate)
    {
        $this->expectException(InvalidWorkingDayException::class);
        $this->dueDateCalculator->calculateDueDate($submitDate, 1);
    }

    public function invalidWorkingHourProvider()
    {
        return [
            "beforeStartOfWork" => [new DateTime('2021-02-01 08:59')],
            "afterStartOfWork" => [new DateTime('2021-02-01 17:01')],
        ];
    }

    /**
     * @dataProvider invalidWorkingHourProvider
     */
    public function testInvalidWorkingHour(DateTime $submitDate)
    {
        $this->expectException(InvalidWorkingHourException::class);
        $this->dueDateCalculator->calculateDueDate($submitDate, 1);
    }

    public function testInvalidTurnaroundTime()
    {
        $this->expectException(InvalidTurnaroundTimeException::class);
        $this->dueDateCalculator->calculateDueDate(new DateTime('2021-02-01 12:00'), -1);
    }

    public function dueDateIsOnSameDayProvider()
    {
        return [
            'MondayNoon' => [new DateTime('2021-02-01 12:00'), 4, new DateTime('2021-02-01 16:00')],
            'TuesdayNoon' => [new DateTime('2021-02-02 12:00'), 4, new DateTime('2021-02-02 16:00')],
            'WednesdayNoon' => [new DateTime('2021-02-03 12:00'), 4, new DateTime('2021-02-03 16:00')],
            'ThursdayNoon' => [new DateTime('2021-02-04 12:00'), 4, new DateTime('2021-02-04 16:00')],
            'FridayNoon' => [new DateTime('2021-02-05 12:00'), 4, new DateTime('2021-02-05 16:00')],
        ];
    }

    /**
     * @dataProvider dueDateIsOnSameDayProvider
     */
    public function testDueDateIsOnSameDay($submitDate, $turnaroundTime, $expectedDueDate)
    {
        $dueDue = $this->dueDateCalculator->calculateDueDate($submitDate, $turnaroundTime);
        $this->assertEquals($expectedDueDate, $dueDue);
    }

    public function dueDateIsOnTheNextWorkingDayProvider()
    {
        return [
            'Monday' => [new DateTime('2021-02-01 11:59'), 8, new DateTime('2021-02-02 11:59')],
            'Tuesday' => [new DateTime('2021-02-02 12:00'), 8, new DateTime('2021-02-03 12:00')],
            'Wednesday' => [new DateTime('2021-02-03 13:00'), 8, new DateTime('2021-02-04 13:00')],
            'Thursday' => [new DateTime('2021-02-04 14:15'), 8, new DateTime('2021-02-05 14:15')],
            'Friday' => [new DateTime('2021-02-05 16:30'), 8, new DateTime('2021-02-08 16:30')],
        ];
    }

    /**
     * @dataProvider dueDateIsOnTheNextWorkingDayProvider
     */
    public function testDueDateIsOnTheNextWorkingDay($submitDate, $turnaroundTime, $expectedDueDate)
    {
        $dueDue = $this->dueDateCalculator->calculateDueDate($submitDate, $turnaroundTime);
        $this->assertEquals($expectedDueDate, $dueDue);
    }

    public function dueDateIsOnTheNextWeekProvider()
    {
        return [
            'Monday' => [new DateTime('2021-02-01 12:00'), 40, new DateTime('2021-02-08 12:00')],
            'Tuesday' => [new DateTime('2021-02-02 12:00'), 40, new DateTime('2021-02-09 12:00')],
            'Wednesday' => [new DateTime('2021-02-03 12:00'), 40, new DateTime('2021-02-10 12:00')],
            'Thursday' =>  [new DateTime('2021-02-04 12:00'), 40, new DateTime('2021-02-11 12:00')],
            'Friday' => [new DateTime('2021-02-05 12:00'), 40, new DateTime('2021-02-12 12:00')],
        ];
    }

    /**
     * @dataProvider dueDateIsOnTheNextWeekProvider
     */
    public function testDueDateIsOnTheNextWeek($submitDate, $turnaroundTime, $expectedDueDate)
    {
        $dueDue = $this->dueDateCalculator->calculateDueDate($submitDate, $turnaroundTime);
        $this->assertEquals($expectedDueDate, $dueDue);
    }

    public function testCalculateDueDateJustAfterTheStartOfTheWorkingDay()
    {
        $dueDue = $this->dueDateCalculator->calculateDueDate(new DateTime('2021-02-01 12:01'), 5);
        $this->assertEquals(new DateTime('2021-02-02 09:01'), $dueDue);
    }

    public function testCalculateDueDateJustBeforeTheEndOfTheWorkingDay()
    {
        $dueDue = $this->dueDateCalculator->calculateDueDate(new DateTime('2021-02-01 11:59'), 5);
        $this->assertEquals(new DateTime('2021-02-01 16:59'), $dueDue);
    }

    public function testCalculateDueDateOnTheEndOfTheWorkingDay()
    {
        $dueDue = $this->dueDateCalculator->calculateDueDate(new DateTime('2021-02-01 12:00'), 5);
        $this->assertEquals(new DateTime('2021-02-01 17:00'), $dueDue);
    }

    public function testCalculateDueDateWithZeroTurnaroundTime()
    {
        $dueDue = $this->dueDateCalculator->calculateDueDate(new DateTime('2021-02-01 12:01'), 0);
        $this->assertEquals(new DateTime('2021-02-01 12:01'), $dueDue);
    }

    public function testCalculateDueDateWithBigTurnaroundTime()
    {
        $dueDue = $this->dueDateCalculator->calculateDueDate(new DateTime('2021-02-01 12:01'), 320);
        $this->assertEquals(new DateTime('2021-03-29 12:01'), $dueDue);
    }

}
