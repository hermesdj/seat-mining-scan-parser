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
use RecursiveTree\Seat\PricesCore\Facades\PriceProviderSystem;
use Seat\Web\Http\Controllers\Controller;

class MiningScanParserController extends Controller
{
    public function parser(Request $request): View|Factory|Application
    {
        return view('scan-parser::parser', [
            'items' => '',
            'price_provider' => null
        ]);
    }

    public function parse(Request $request): View|Factory|Application|RedirectResponse
    {
        $request->validate([
            'items' => 'required',
            'price_provider' => 'numeric'
        ]);

        $items = $request->get('items');

        $result = MineralScanParser::parse($items, PriceableEveItem::class);

        if ($request->get('price_provider') !== null) {
            PriceProviderSystem::getPrices($request->get('price_provider'), $result->items);
        }

        $formatted = $this->formatResult($result->items);

        logger()->debug(print_r($formatted, true));

        return view('scan-parser::parser-result', [
            'result' => $formatted,
            'items' => $items,
            'price_provider' => $request->get('price_provider')
        ]);
    }

    private function formatResult(Collection $itemData): Collection
    {
        $parsedItems = [];

        foreach ($itemData as $item) {
            if (isset($parsedItems[$item->getTypeID()])) {
                $parsedItems[$item->getTypeID()]['typeQuantity'] += $item->amount;
                $parsedItems[$item->getTypeID()]['volume'] += $item->volume * $item->amount;
                $parsedItems[$item->getTypeID()]['total'] += $item->amount * $item->price;
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
                $parsedItems[$item->getTypeID()]["unit_price"] = $item->price;
                $parsedItems[$item->getTypeID()]["total"] = $item->price * $item->amount;
            }
        }

        return collect($parsedItems)->sortBy('marketGroupName');
    }
}