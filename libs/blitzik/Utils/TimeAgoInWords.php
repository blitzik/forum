<?php declare(strict_types=1);

namespace blitzik\Utils;

use Nette\Object;

class TimeAgoInWords extends Object
{
    public static function get($time)
    {
        if (!$time) {
            return false;

        } elseif (is_numeric($time)) {
            $time = (int) $time;

        } elseif ($time instanceof \DateTime or $time instanceof \DateTimeImmutable) {
            $time = $time->format('U');
            
        } else {
            $time = strtotime($time);
        }

        $delta = time() - $time;
        if ($delta < 0) {
            $delta = round(abs($delta) / 60);
            if ($delta == 0) return 'in a while';
            if ($delta == 1) return 'in a minute';
            if ($delta < 45) return 'in ' . $delta . ' ' . self::plural($delta, 'minute', 'minutes');
            if ($delta < 90) return 'in an hour';
            if ($delta < 1440) return 'in ' . round($delta / 60) . ' ' . self::plural(round($delta / 60), 'hour', 'hours');
            if ($delta < 2880) return 'tomorrow';
            if ($delta < 43200) return 'in ' . round($delta / 1440) . ' ' . self::plural(round($delta / 1440), 'day', 'days');
            if ($delta < 86400) return 'in a month';
            if ($delta < 525960) return 'in ' . round($delta / 43200) . ' ' . self::plural(round($delta / 43200), 'month', 'months');
            if ($delta < 1051920) return 'in a year';

            return 'in ' . round($delta / 525960) . ' ' . self::plural(round($delta / 525960), 'year', 'years');
        }

        $delta = round($delta / 60);
        if ($delta == 0) return 'a while ago';
        if ($delta == 1) return 'a minute ago';
        if ($delta < 45) return $delta . ' minutes ago';
        if ($delta < 90) return 'an hour ago';
        if ($delta < 1440) return round($delta / 60) . ' hours ago';
        if ($delta < 2880) return 'yesterday';
        if ($delta < 43200) return round($delta / 1440) . ' days ago';
        if ($delta < 86400) return 'a month ago';
        if ($delta < 525960) return round($delta / 43200) . ' months ago';
        if ($delta < 1051920) return 'a year ago';

        return round($delta / 525960) . ' years ago';
    }


    private static function plural($n)
    {
        $args = func_get_args();
        return $args[($n == 1) ? 1 : 2];
    }
}