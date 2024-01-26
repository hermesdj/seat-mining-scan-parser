<?php

namespace HermesDj\Seat\SeatMiningScanParser\Parser;

use RecursiveTree\Seat\TreeLib\Parser\Parser;
use Seat\Eveapi\Models\Sde\InvType;

class MineralScanParser extends Parser
{
    protected const BIG_NUMBER_REGEXP = "(?:\d+(?:[’\s+,]\d\d\d)*(?:\.\d\d)?)";

    public static function parse(string $text, string $EveItemClass): ?MineralScanParseResult
    {
        $text = preg_replace('~\R~u', "\n", $text);
        $text = preg_replace('~\x{b3}~u', '3', $text);
        $text = preg_replace('/\xc2\xa0/', ' ', $text);

        $expr = implode("", [
            "^(?<name>[^\t*]+)\*?",           // Name
            "\t(?<amount>" . self::BIG_NUMBER_REGEXP . "?)", // amount
            "(?:\t(?<volume>" . self::BIG_NUMBER_REGEXP . ")\sm.)?",         //volume
            "(?:\t(?<distance>" . self::BIG_NUMBER_REGEXP . "))?\s(?<dist_type>k?m)", // distance
            "$"
        ]);

        $lines = self::matchLines($expr, $text);

        if ($lines->where("match", "!=", null)->isEmpty()) return null;

        $warning = false;
        $parsed = [];
        $unparsed = [];

        foreach ($lines as $line) {
            if ($line->match === null) {
                logger()->warning("No match line " . print_r($line, true));
                continue;
            }

            $inv_model = InvType::where("typeName", $line->match->name)->first();

            $amount = self::parseBigNumber($line->match->amount);
            if ($amount == null) $amount = 1;
            if ($amount < 1) $amount = 1;

            $volume = self::parseBigNumber($line->match->volume);
            if ($volume !== null) $volume = $volume / $amount;

            $distance = self::parseBigNumber($line->match->distance);

            if ($line->match->dist_type === 'km') {
                $distance = $distance * 1000;
            }

            if ($inv_model == null) {
                $warning = true;
                $unparsed[] = [
                    'name' => $line->match->name,
                    'amount' => $amount,
                    'volume' => $volume,
                    'distance' => $distance
                ];
                logger()->warning("Ignored line " . print_r($line, true));
                continue;
            }

            $item = new $EveItemClass($inv_model);
            $item->amount = $amount;
            $item->volume = $volume;
            $item->distance = $distance;

            $parsed[] = $item;
        }

        if (count($parsed) < 1 && count($unparsed) < 1) return null;

        $result = new MineralScanParseResult(collect($parsed), collect($unparsed));
        $result->warning = $warning;

        return $result;
    }

    protected static function parseBigNumber($number): ?float
    {
        if ($number === null) return null;
        return floatval(str_replace(["’", " ", ','], "", $number));
    }
}