<?php

namespace DueDate\Validator;

use DateTime;
use DueDate\Exception\InvalidWorkingDayException;
use DueDate\Exception\InvalidWorkingHourException;

class SubmitDateValidator
{

    private const NUMBER_OF_WEEKEND_DAYS = array(6,7);

    private string $workingDayStartTime;
    private string $workingDayEndTime;

    private function __construct(string $workingDayStartTime, string $workingDayEndTime)
    {
        $this->workingDayStartTime = $workingDayStartTime;
        $this->workingDayEndTime = $workingDayEndTime;
    }

    public static function withWorkingDayStartAndEndTime(string $workingDayStartTime, string $workingDayEndTime)
    {
        return new SubmitDateValidator($workingDayStartTime, $workingDayEndTime);
    }

    public function validate(DateTime $submitDate)
    {
        if (!$this->isDateOnWorkingDay($submitDate)) {
            throw new InvalidWorkingDayException(sprintf("Submit date should be on a workday!"));
        }
        if (!$this->isTimeInWorkingHourWindow($submitDate)) {
            throw new InvalidWorkingHourException(sprintf("Submit date should be between %s and %s!",
                $this->workingDayStartTime,
                $this->workingDayEndTime));
        }
    }

    public function isDateOnWorkingDay(DateTime $date): bool
    {
        return !in_array($date->format("N"), static::NUMBER_OF_WEEKEND_DAYS);
    }

    public function isTimeInWorkingHourWindow(DateTime $date): bool
    {
        $workHour = $date->format("H:i:s");
        return $workHour > $this->workingDayStartTime
            && $workHour <= $this->workingDayEndTime;
    }
}