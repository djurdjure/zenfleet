@extends('layouts.admin.catalyst')
@section('title', 'Nouvelle Organisation - ZenFleet')

@section('content')
<x-organization-form
    :organization="null"
    :isEdit="false"
    :countries="[]"
    :organizationTypes="[
        'Grande Entreprise' => 'Grande Entreprise',
        'PME' => 'PME',
        'Association' => 'Association',
        'StartUp' => 'StartUp',
        'ONG' => 'ONG',
        'Cooperative' => 'Cooperative',
        'Société Public' => 'Société Public'
    ]"
    :currencies="[]"
    :timezones="[]"
    :subscriptionPlans="[]"
/>
@endsection