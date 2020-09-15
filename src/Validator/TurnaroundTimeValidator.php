<?php

namespace DueDate\Validator;

use DueDate\Exception\InvalidTurnaroundTimeException;

class TurnaroundTimeValidator
{
    public function validate(int $turnaroundTime)
    {
        if ($turnaroundTime < 0) {
            throw new InvalidTurnaroundTimeException(sprintf("Turnaround time should be a zero or a positive whole number!"));
        }
    }

}