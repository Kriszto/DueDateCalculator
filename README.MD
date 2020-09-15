### DueDateCalculator

The project implements the calculateDueDate method according to the given description.

##### The `calculateDueDate()` method
Implements a due date calculator in an issue tracking system.

###### Inputs:
- `submitDate`: standard PHP `DateTime` object
- `turnaroundTimeInHours`: `integer`

###### Output

The method returns a standard PHP `DateTime` object with the calculated due date.

##### Example usage:
```
$calculator = new DueDate\Calculator();
$submitDate = new DateTime('2021-02-01 12:00');
$turnaroundTime = 8;
$dueDate = $this->calculator->calculateDueDate($submitDate, $turnaroundTime);
```

##### Tests
Automated PHPUnit tests are included in `/tests` directory.  
Test coverage is 100%.

##### Environment
- PHP 7.4.9
- Xdebug v2.9.6
- PHPUnit 9.3.10


