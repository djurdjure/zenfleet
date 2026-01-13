<x-guest-layout>
    <!-- Modern Minimal Enterprise Login -->
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">

            <!-- Logo Placeholder Section -->
            <div class="flex flex-col items-center justify-center space-y-6">
                <!-- Logo Placeholder - Can be replaced with actual logo -->
                <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center shadow-sm">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>

                <!-- Brand Name -->
                <div class="text-center">
                    <h1 class="text-2xl font-semibold text-gray-900 tracking-tight">
                        ZenFleet
                    </h1>
                    <p class="mt-2 text-sm text-gray-500">
                        Connectez-vous à votre compte
                    </p>
                </div>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6" x-data="{ isLoading: false }" @submit="isLoading = true">
                @csrf

                <div class="space-y-5">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="nom@entreprise.com"
                            class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400
                                   focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent
                                   transition-all duration-200 text-gray-900 text-base
                                   @error('email') border-red-300 focus:ring-red-500 @enderror" />
                        @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Mot de passe
                        </label>
                        <div class="relative" x-data="{ showPassword: false }">
                            <input
                                id="password"
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••••"
                                class="appearance-none block w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400
                                       focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent
                                       transition-all duration-200 text-gray-900 text-base
                                       @error('password') border-red-300 focus:ring-red-500 @enderror" />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                                <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="flex items-center cursor-pointer group">
                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-600 border-gray-300 rounded transition-colors cursor-pointer" />
                        <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">
                            Se souvenir de moi
                        </span>
                    </label>

                    @if (Route::has('password.request'))
                    <a
                        href="{{ route('password.request') }}"
                        class="text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                        Mot de passe oublié ?
                    </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    :disabled="isLoading"
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent rounded-lg
                           text-sm font-medium text-white bg-blue-600
                           hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600
                           transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed
                           shadow-sm hover:shadow-md">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-4" x-show="isLoading">
                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    <span x-text="isLoading ? 'Connexion...' : 'Se connecter'">
                        Se connecter
                    </span>
                </button>

                <!-- Optional: Register Link -->
                @if (Route::has('register'))
                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600">
                        Pas encore de compte ?
                        <a
                            href="{{ route('register') }}"
                            class="font-medium text-gray-900 hover:text-gray-700 transition-colors">
                            Créer un compte
                        </a>
                    </p>
                </div>
                @endif
            </form>

            <!-- Footer -->
            <div class="text-center pt-8">
                <p class="text-xs text-gray-400">
                    © {{ date('Y') }} ZenFleet. Tous droits réservés.
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>