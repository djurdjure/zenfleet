{{-- resources/views/profile/partials/update-profile-information-form.blade.php --}}
<section>
 <header>
 <h2 class="text-lg font-medium text-gray-900 ">
 {{ __('Informations du Profil') }}
 </h2>

 <p class="mt-1 text-sm text-gray-600 ">
 {{ __("Mettez à jour les informations de profil et l'adresse e-mail de votre compte.") }}
 </p>
 </header>

 <form id="send-verification" method="post" action="{{ route('verification.send') }}">
 @csrf
 </form>

 <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
 @csrf
 @method('patch')

 {{-- First Name --}}
 <div>
 <x-input-label for="first_name" :value="__('Prénom')" />
 <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $user->first_name)" required autofocus autocomplete="given-name" />
 <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
 </div>

 {{-- Last Name --}}
 <div>
 <x-input-label for="last_name" :value="__('Nom de famille')" />
 <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $user->last_name)" required autocomplete="family-name" />
 <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
 </div>

 {{-- Email Address --}}
 <div>
 <x-input-label for="email" :value="__('Email')" />
 <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" /> {{-- Breeze utilise 'username' pour l'autocomplete de l'email dans ce contexte --}}
 <x-input-error class="mt-2" :messages="$errors->get('email')" />

 @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
 <div>
 <p class="text-sm mt-2 text-gray-800 ">
 {{ __('Votre adresse e-mail n\'est pas vérifiée.') }}

 <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 :text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 :ring-offset-gray-800">
 {{ __('Cliquez ici pour renvoyer l\'e-mail de vérification.') }}
 </button>
 </p>

 @if (session('status') === 'verification-link-sent')
 <p class="mt-2 font-medium text-sm text-green-600 ">
 {{ __('Un nouveau lien de vérification a été envoyé à votre adresse e-mail.') }}
 </p>
 @endif
 </div>
 @endif
 </div>

 {{-- Phone Number --}}
 <div>
 <x-input-label for="phone" :value="__('Téléphone (optionnel)')" />
 <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" autocomplete="tel" />
 <x-input-error class="mt-2" :messages="$errors->get('phone')" />
 </div>

 <div class="flex items-center gap-4">
 <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>

 @if (session('status') === 'profile-updated')
 <p
 x-data="{ show: true }"
 x-show="show"
 x-transition
 x-init="setTimeout(() => show = false, 2000)"
 class="text-sm text-gray-600 "
 >{{ __('Enregistré.') }}</p>
 @endif
 </div>
 </form>
</section>
