@extends('layouts.admin.catalyst')
@section('title', 'Modifier ' . $organization->name)

@section('content')
<x-organization-form
    :organization="$organization"
    :isEdit="true"
    :countries="$countries ?? []"
    :organizationTypes="$organizationTypes ?? []"
    :currencies="$currencies ?? []"
    :timezones="$timezones ?? []"
    :subscriptionPlans="$subscriptionPlans ?? []"
/>
@endsection
