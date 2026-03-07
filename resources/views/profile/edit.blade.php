@extends('layouts.admin.catalyst')

@section('title', 'Mon Profil')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div>
        <h1 class="text-xl font-bold text-gray-600">Mon profil</h1>
        <p class="text-xs text-gray-600">Mettez à jour vos informations de compte.</p>
    </div>

    @if (session('status') === 'profile-updated')
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            Profil mis à jour avec succès.
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-sm font-semibold text-slate-600">Informations personnelles</h2>
        <p class="mt-0.5 text-xs text-slate-400">Ces données sont utilisées dans votre session ZenFleet.</p>

        <form method="post" action="{{ route('profile.update') }}" class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
            @csrf
            @method('patch')

            <div>
                <label for="first_name" class="mb-2 block text-sm font-medium text-gray-600">Prénom</label>
                <input id="first_name" name="first_name" type="text"
                    value="{{ old('first_name', $user->first_name) }}"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-[#0c90ee] focus:outline-none focus:ring-2 focus:ring-[#0c90ee]/20">
            </div>

            <div>
                <label for="last_name" class="mb-2 block text-sm font-medium text-gray-600">Nom</label>
                <input id="last_name" name="last_name" type="text"
                    value="{{ old('last_name', $user->last_name) }}"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-[#0c90ee] focus:outline-none focus:ring-2 focus:ring-[#0c90ee]/20">
            </div>

            <div class="md:col-span-2">
                <label for="email" class="mb-2 block text-sm font-medium text-gray-600">Email</label>
                <input id="email" name="email" type="email"
                    value="{{ old('email', $user->email) }}"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-[#0c90ee] focus:outline-none focus:ring-2 focus:ring-[#0c90ee]/20">
            </div>

            <div class="md:col-span-2">
                <label for="phone" class="mb-2 block text-sm font-medium text-gray-600">Téléphone</label>
                <input id="phone" name="phone" type="text"
                    value="{{ old('phone', $user->phone) }}"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-[#0c90ee] focus:outline-none focus:ring-2 focus:ring-[#0c90ee]/20">
            </div>

            <div class="md:col-span-2 flex justify-end gap-3 border-t border-gray-100 pt-4">
                <button type="submit"
                    class="inline-flex items-center rounded-lg bg-[#0c90ee] px-4 py-2 text-sm font-medium text-white hover:bg-[#0b82d6] focus:outline-none focus:ring-2 focus:ring-[#0c90ee]/30">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>

    <div class="rounded-xl border border-red-200 bg-white p-6 shadow-sm">
        <h2 class="text-sm font-semibold text-red-700">Supprimer le compte</h2>
        <p class="mt-0.5 text-xs text-red-400">Action irréversible. Entrez votre mot de passe pour confirmer.</p>

        <form method="post" action="{{ route('profile.destroy') }}" class="mt-4 space-y-4">
            @csrf
            @method('delete')

            <div>
                <label for="password" class="mb-2 block text-sm font-medium text-gray-600">Mot de passe actuel</label>
                <input id="password" name="password" type="password"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20">
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="inline-flex items-center rounded-lg border border-red-300 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500/30">
                    Supprimer mon compte
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
