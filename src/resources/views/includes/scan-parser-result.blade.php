<div class="card">
    <div class="card-body">
        <table id="parse-result" class="table">
            <thead>
            <tr>
                <th>{{trans('scan-parser::common.columns.name')}}</th>
                <th>{{trans('scan-parser::common.columns.group')}}</th>
                <th>{{trans('scan-parser::common.columns.amount')}}</th>
                <th>{{trans('scan-parser::common.columns.volume')}}</th>
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
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>