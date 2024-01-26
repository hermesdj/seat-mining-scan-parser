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
            @if($price_provider !== null)
                <tfoot>
                <tr>
                    <th colspan="5" class="text-right">{{trans('scan-parser::common.columns.total')}}</th>
                    <th colspan="1" class="text-right">
                        {{number_format($result->reduce(function($carry, $item) {
                         return $carry + $item['total'];
                        }), 2, ',', ' ')}} ISK
                    </th>
                </tr>
                </tfoot>
            @endif
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
            </table>
        </div>
    </div>
@endif