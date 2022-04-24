<?php

namespace Tests;

class DateTimeFactory
{
    /**
     * @param string $datetime
     * @param string $timezone
     * @return \DateTime
     * @description creates a DateTime with a time modifier in ISO 8601 format AND a timezone
     * @see https://www.php.net/manual/en/datetime.createfromformat.php
     */
    public static function iso8601(string $datetime, string $timezone): \DateTime
    {
        $date = \DateTime::createFromFormat(
            \DateTimeInterface::ISO8601,
            $datetime
        );
        $date->setTimezone(new \DateTimeZone($timezone));

        return $date;
    }
}