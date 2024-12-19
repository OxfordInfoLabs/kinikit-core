<?php

namespace Kinikit\Core\Template\ValueFunction;

use Kinikit\Core\Util\DateTimeUtils;

class DateFormatValueFunction extends ValueFunctionWithArguments {

    const supportedFunctions = [
        "ensureDateFormat",
        "dateConvert",
        "dayOfMonth",
        "dayOfWeek",
        "dayName",
        "monthName",
        "month",
        "year",
        "date",
        "dateAdd",
        "dateSub",
        "formattedDuration",
        "now"
    ];

    const DURATIONS = [
        [
            "key" => "milliseconds",
            "label" => "Millisecond",
            "multiplier" => 1
        ], [
            "key" => "seconds",
            "label" => "Second",
            "multiplier" => 1000
        ], [
            "key" => "minutes",
            "label" => "Minute",
            "multiplier" => 60000
        ], [
            "key" => "hours",
            "label" => "Hour",
            "multiplier" => 3600000
        ], [
            "key" => "days",
            "label" => "Day",
            "multiplier" => 86400000
        ]];


    /**
     * Get supported function names
     *
     * @return string[]|void
     */
    protected function getSupportedFunctionNames() {
        return self::supportedFunctions;
    }

    /**
     * Apply function with args
     *
     * @param $functionName
     * @param $functionArgs
     * @param $value
     * @return mixed|void
     */
    protected function applyFunctionWithArgs($functionName, $functionArgs, $value, $model) {

        $value = $value ?? "";
        $standardDate = date_create_from_format("Y-m-d", substr($value, 0, 10));

        switch ($functionName) {
            case "ensureDateFormat":
                $date = date_create_from_format($functionArgs[0] ?? "", $value);
                return $date ? $value : null;
            case "now":
                return date($functionArgs[0] ?? 'Y-m-d H:i:s');
            case "dateConvert":
                $date = date_create_from_format($functionArgs[0] ?? "", $value);
                return $date && ($functionArgs[1] ?? null) ? $date->format($functionArgs[1]) : null;
            case "dayOfMonth":
                return $standardDate ? $standardDate->format("d") : null;
            case "dayOfWeek":
                return $standardDate ? $standardDate->format("w") + 1 : null;
            case "dayName":
                return $standardDate ? $standardDate->format("l") : null;
            case "month":
                return $standardDate ? $standardDate->format("m") : null;
            case "monthName":
                if (is_numeric($value)) {
                    $date = date_create_from_format("d/m/Y", "01/$value/2000");
                    return $date ? $date->format("F") : null;
                } else
                    return $standardDate ? $standardDate->format("F") : null;
            case "year":
                return $standardDate ? $standardDate->format("Y") : null;
            case "date":
                $date = date_create($value);
                $format = DateTimeUtils::convertJSDateFormatToPHP($functionArgs[0] ?? "");
                return $date->format($format);
            case  "dateAdd":
                $date = date_create($value);
                $dateInterval = $this->createDateInterval($functionArgs[0], $functionArgs[1] ?? 0);
                return $date->add($dateInterval);
            case  "dateSub":
                $date = date_create($value);
                $dateInterval = $this->createDateInterval($functionArgs[0], $functionArgs[1] ?? 0);
                return $date->sub($dateInterval);

            case "formattedDuration":
                if (!is_numeric($value)) {
                    return $value;
                }

                $unitDivisor = null;
                foreach (self::DURATIONS as $duration) {
                    if ($duration["key"] == ($functionArgs[0] ?? "milliseconds")) {
                        $unitDivisor = $duration["multiplier"];
                    }
                }

                $expressions = [];
                if ($unitDivisor) {
                    for ($i = sizeof(self::DURATIONS) - 1; $i >= 0; $i--) {
                        $durationValue = $value / self::DURATIONS[$i]["multiplier"] * $unitDivisor;
                        if ($durationValue >= 1) {
                            $duration = floor($durationValue);
                            $expressions[] = $duration . " " . self::DURATIONS[$i]["label"] . ($duration > 1 ? "s" : "");
                            $value -= $duration * self::DURATIONS[$i]["multiplier"]; // This line not in ts version but not sure why
                        }
                        if (sizeof($expressions) >= ($functionArgs[1] ?? 10)) {
                            break;
                        }
                    }
                }

                return join(" ", $expressions);

            default:
                return $value;
        }
    }

    private function createDateInterval($period, $quantity) {

        switch ($period) {
            case "seconds":
                return new \DateInterval("PT" . $quantity . "S");
            case "minutes":
                return new \DateInterval("PT" . $quantity . "M");
            case "hours":
                return new \DateInterval("PT" . $quantity . "H");
            case "days":
                return new \DateInterval("P" . $quantity . "D");
            case "weeks":
                return new \DateInterval("P" . $quantity . "W");
            case "months":
                return new \DateInterval("P" . $quantity . "M");
            case "years":
                return new \DateInterval("P" . $quantity . "Y");
            default:
                return null;
        }
    }
}