<?php


namespace Tests\Fakes;


class CsvContentFake
{
    public static function getContent()
    {
        return <<<CSV
Label,Value,Date
"Car Insurance",-185.15,"2016-01-16 18:02:17"
"Groceries",-69.52,"1986-07-20 04:17:58"
"Rent",-148.91,"1975-07-25 11:02:59"
CSV;
    }
}
