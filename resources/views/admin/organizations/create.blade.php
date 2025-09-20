@extends('layouts.admin.catalyst')
@section('title', 'Nouvelle Organisation - ZenFleet')

@section('content')
<x-organization-form-algeria
    :organization="null"
    :isEdit="false"
    :wilayas="$wilayas"
    :organizationTypes="$organizationTypes"
/>
@endsection