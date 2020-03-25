@extends('layouts.app')

@section('headerMenu')
  	<headermenu></headermenu>
@endsection

@section('notification')
	<notification></notification>
@endsection

@section('content')

<!-- contents from vue js -->
<router-view></router-view>

@endsection

@push('vueScripts')
<!-- Vue js -->
<script type="text/javascript" src="{{asset('js/system.js')}}"></script>
@endpush