<?php

namespace HermesDj\Seat\SeatMiningScanParser\Http\Controllers;

use HermesDj\Seat\SeatMiningScanParser\Item\PriceableEveItem;
use HermesDj\Seat\SeatMiningScanParser\Parser\MineralScanParser;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Seat\Web\Http\Controllers\Controller;

class MiningScanParserController extends Controller
{
    public function parser(Request $request): View|Factory|Application
    {
        return view('scan-parser::parser', [
            'items' => ''
        ]);
    }

    public function parse(Request $request): View|Factory|Application|RedirectResponse
    {
        $request->validate([
            'items' => 'required'
        ]);

        $items = $request->get('items');

        $result = MineralScanParser::parse($items, PriceableEveItem::class);

        return view('scan-parser::parser-result', [
            'result' => $this->formatResult($result->items),
            'items' => $items
        ]);
    }

    private function formatResult(Collection $itemData): Collection
    {
        $parsedItems = [];

        foreach ($itemData as $item) {
            if (isset($parsedItems[$item->getTypeID()])) {
                $parsedItems[$item->getTypeID()]['typeQuantity'] += $item->amount;
                $parsedItems[$item->getTypeID()]['volume'] += $item->volume * $item->amount;
            } else {
                $result = DB::table('invTypes as it')
                    ->join('invGroups as ig', 'it.groupID', '=', 'ig.GroupID')
                    ->select(
                        'it.typeID as typeID',
                        'it.typeName as typeName',
                        'it.description as description',
                        'ig.GroupName as groupName',
                        'ig.GroupID as groupID',
                        'it.volume as volume'
                    )
                    ->where('it.typeID', '=', $item->getTypeID())
                    ->first();

                if (empty($result)) {
                    Log::debug("Ignore item .$item->typeModel->typeName");
                    continue;
                }

                $parsedItems[$item->getTypeID()]["typeId"] = $item->typeModel->typeID;
                $parsedItems[$item->getTypeID()]["typeName"] = $item->typeModel->typeName;
                $parsedItems[$item->getTypeID()]["typeQuantity"] = $item->amount;
                $parsedItems[$item->getTypeID()]["groupId"] = $item->typeModel->groupID;
                $parsedItems[$item->getTypeID()]["marketGroupName"] = $result->groupName;
                $parsedItems[$item->getTypeID()]["volume"] = $result->volume * $item->amount;
            }
        }

        return collect($parsedItems)->sortBy('marketGroupName');
    }
}