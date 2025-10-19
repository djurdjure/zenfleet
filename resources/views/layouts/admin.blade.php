{{--
 ALIAS VERS LE LAYOUT ADMIN PRINCIPAL CATALYST
 Ce fichier redirige vers le layout principal admin
 Pour des raisons de compatibilité avec les vues utilisant @extends('layouts.admin')
--}}
@extends('layouts.admin.catalyst')

@section('content')
 @yield('content')
@endsection
