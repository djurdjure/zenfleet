@extends('layouts.admin.catalyst')
@section('title', 'Modifier ' . $organization->name)

@section('content')
<x-organization-form-enterprise
    :organization="$organization"
    :isEdit="true"
    :wilayas="$wilayas ?? []"
    :organizationTypes="$organizationTypes ?? []"
/>
@endsection
