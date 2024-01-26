<div class="card">
    <div class="card-body">
        <form action="{{route('scan-parser::parse')}}" method="post" id="parse" name="parse">
            {{csrf_field()}}
            <div class="form-group">
                <label for="items">{{trans('scan-parser::common.form.input')}}</label>
                <p>
                    <small>{{trans('scan-parser::common.form.desc')}}</small>
                </p>
                <textarea id="items" class="w-100" name="items" rows="10">{{$items}}</textarea>
            </div>
            <button type="submit" class="btn btn-primary" form="parse">
                {{trans('scan-parser::common.form.parse')}}
            </button>
        </form>
    </div>
</div>