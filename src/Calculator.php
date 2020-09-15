<?php

namespace DueDate;

use DateInterval;
use DateTime;
use DueDate\Validator\SubmitDateValidator;
use DueDate\Validator\TurnaroundTimeValidator;

final class Calculator
{

    private const WORKING_DAY_START_TIME = "09:00:00";
    private const WORKING_DAY_END_TIME = "17:00:00";


    private DateTime $dueDate;

    private int $workingHoursPerDay;

    private SubmitDateValidator $submitDateValidator;
    private TurnaroundTimeValidator $turnaroundTimeValidator;

    public function __construct()
    {
        $this->submitDateValidator = SubmitDateValidator::withWorkingDayStartAndEndTime(
            static::WORKING_DAY_START_TIME,
            static::WORKING_DAY_END_TIME
        );
        $this->turnaroundTimeValidator = new TurnaroundTimeValidator();
        $this->workingHoursPerDay = $this->getWorkingHoursPerDayFromStartAndEndTime();
    }

    public function calculateDueDate(DateTime $submitDate, int $turnaroundTimeInHours)
    {
        $this->submitDateValidator->validate($submitDate);
        $this->turnaroundTimeValidator->validate($turnaroundTimeInHours);
        $this->dueDate = $submitDate;
        $this->addTurnaroundTimeToDueDate($turnaroundTimeInHours);
        return $this->dueDate;
    }

    private function addTurnaroundTimeToDueDate(int $turnaroundTime): void
    {
        $this->addDaysToDueDate(intdiv($turnaroundTime, $this->workingHoursPerDay));
        $this->addHoursToDueDate($turnaroundTime % $this->workingHoursPerDay);
    }


    private function addDaysToDueDate(int $numberOfDaysToAdd): void
    {
        while ($numberOfDaysToAdd > 0) {
            $this->addOneWorkingDayToDueDate();
            $numberOfDaysToAdd -= 1;
        }
    }

    private function addHoursToDueDate(int $numberOfHoursToAdd): void
    {
        while ($numberOfHoursToAdd > 0) {
            $this->addOneWorkingHourToDueDate();
            $numberOfHoursToAdd -= 1;
        }
    }

    private function addOneWorkingDayToDueDate(): void
    {
        $this->addOneDayToDueDate();
        $this->adjustDueDateToNextWorkingDayIfNeeded();
    }

    private function addOneWorkingHourToDueDate(): void
    {
        $this->addOneHourToDueDate();
        $this->adjustDueDateToNextWorkingHourIfNeeded();
    }

    private function addOneHourToDueDate(): void
    {
        $this->dueDate->add(new DateInterval('PT1H'));
    }

    private function addOneDayToDueDate(): void
    {
        $this->dueDate->add(new DateInterval('P1D'));
    }


    private function adjustDueDateToNextWorkingDayIfNeeded(): void
    {
        while (!$this->submitDateValidator->isDateOnWorkingDay($this->dueDate)) {
            $this->addOneDayToDueDate();
        }
    }

    private function adjustDueDateToNextWorkingHourIfNeeded(): void
    {
        while (!$this->submitDateValidator->isTimeInWorkingHourWindow($this->dueDate)) {
            $this->addOneHourToDueDate();

        }
    }

    private function getWorkingHoursPerDayFromStartAndEndTime(): int
    {
        return (new DateTime(static::WORKING_DAY_END_TIME))->diff(new DateTime(static::WORKING_DAY_START_TIME))->h;
    }

}