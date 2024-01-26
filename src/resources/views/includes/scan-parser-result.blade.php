<div class="card">
    <div class="card-body">
        <h5 class="card-title">
            {{trans('scan-parser::common.result.title')}}
        </h5>
        <table id="parse-result" class="table">
            <thead>
            <tr>
                <th>{{trans('scan-parser::common.columns.name')}}</th>
                <th>{{trans('scan-parser::common.columns.group')}}</th>
                <th>{{trans('scan-parser::common.columns.amount')}}</th>
                <th>{{trans('scan-parser::common.columns.volume')}}</th>
                <th>{{trans('scan-parser::common.columns.value')}}</th>
                <th class="text-right">{{trans('scan-parser::common.columns.total')}}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($result as $row)
                <tr>
                    <td><img alt="Item image" src="https://images.evetech.net/types/{{ $row["typeId"] }}/icon?size=32"/>
                        <b>{{$row['typeName']}}</b></td>
                    <td>{{$row['marketGroupName']}}</td>
                    <td>{{number_format($row['typeQuantity'], 0, ',', ' ')}}</td>
                    <td>{{number_format($row['volume'], 2, ',', ' ')}} m3</td>
                    <td>{{number_format($row['unit_price'], 2, ',', ' ')}} ISK</td>
                    <td class="text-right">{{number_format($row['total'], 2, ',', ' ')}} ISK</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            @php
                $totalVolume = $result->reduce(function($carry, $item) {
                 return $carry + $item['volume'];
                });
                $totalValue = $result->reduce(function($carry, $item) {
                 return $carry + $item['total'];
                })
            @endphp
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th>
                    {{ number_format($totalVolume, 2, ',', ' ') }} m3 <br/>
                    ({{number_format($totalVolume / 100, 2, ',', ' ')}} m3 {{trans('scan-parser::common.compressed')}})
                </th>
                @if($price_provider !== null)
                    <th class="text-right">{{trans('scan-parser::common.columns.total')}}</th>
                    <th class="text-right">
                        {{number_format($totalValue, 2, ',', ' ')}} ISK
                    </th>
                @else
                    <th></th>
                    <th></th>
                @endif
            </tr>
            </tfoot>
        </table>
    </div>
</div>
@if(count($unparsed) > 0)
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">
                {{trans('scan-parser::common.unparsed.title')}}
            </h5>
            <table id="parse-result" class="table">
                <thead>
                <tr>
                    <th>{{trans('scan-parser::common.columns.name')}}</th>
                    <th>{{trans('scan-parser::common.columns.amount')}}</th>
                    <th>{{trans('scan-parser::common.columns.volume')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($unparsed as $row)
                    <tr>
                        <td><b>{{$row['name']}}</b></td>
                        <td>{{number_format($row['amount'], 0, ',', ' ')}}</td>
                        <td>{{number_format($row['volume'], 2, ',', ' ')}} m3</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                @php
                    $totalVolumeUnparsed = $unparsed->reduce(function($carry, $item) {
                     return $carry + $item['volume'];
                    });
                @endphp
                <tr>
                    <th></th>
                    <th></th>
                    <th>{{number_format($totalVolumeUnparsed, 2, ',', ' ')}} m3 <br/>
                        ({{number_format($totalVolumeUnparsed / 100, 2, ',', ' ')}}
                        m3 {{trans('scan-parser::common.compressed')}})
                    </th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endif