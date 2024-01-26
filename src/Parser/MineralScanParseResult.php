<?php

namespace HermesDj\Seat\SeatMiningScanParser\Parser;

use Illuminate\Support\Collection;
use RecursiveTree\Seat\TreeLib\Parser\ParseResult;

class MineralScanParseResult extends ParseResult
{
    public Collection $unparsed;


    public function __construct($parsed, $unparsed)
    {
        $this->unparsed = $unparsed;
        parent::__construct($parsed);
    }

    public function __serialize(): array
    {
        return $this->getProperties();
    }

    public function jsonSerialize(): array
    {
        return array_merge($this->getProperties(), [
            "items" => $this->items,
            "unparsed" => $this->unparsed
        ]);
    }
}