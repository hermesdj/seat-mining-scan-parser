@extends('web::layouts.grids.3-9')

@section('title', trans('scan-parser::common.scan.title'))
@section('page_header', trans('scan-parser::common.scan.title'))

@section('left')
    @include("scan-parser::includes.scan-parser-form")
@endsection

