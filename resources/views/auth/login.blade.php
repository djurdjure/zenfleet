<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <!-- 🚀 ZenFleet Enterprise Login -->
    <div class="zenfleet-fade-in">
        <!-- Header Section -->
        <div class="mb-10 text-center">
            <!-- Logo with Enterprise Badge -->
            <div class="relative mb-8">
                <div class="mx-auto w-24 h-24 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-3xl flex items-center justify-center shadow-2xl float border border-white/20">
                    <i class="fas fa-truck text-white text-4xl"></i>
                </div>
                <!-- Enterprise Badge -->
                <div class="absolute -top-2 -right-2 bg-gradient-to-r from-amber-400 to-orange-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow-lg">
                    ENTERPRISE
                </div>
            </div>

            <!-- Brand Name -->
            <h1 class="text-5xl font-black mb-2">
                <span class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 bg-clip-text text-transparent">
                    Zen
                </span><span class="bg-gradient-to-r from-indigo-700 via-blue-800 to-gray-900 bg-clip-text text-transparent">Fleet</span>
            </h1>

            <!-- Subtitle -->
            <div class="space-y-2">
                <p class="text-gray-700 text-lg font-medium">Plateforme Enterprise de Gestion de Flotte</p>
                <div class="flex items-center justify-center gap-3 text-sm">
                    <div class="flex items-center gap-1.5 text-emerald-600 font-medium">
                        <i class="fas fa-shield-check text-sm"></i>
                        <span>Certifié Algérie</span>
                    </div>
                    <div class="w-1 h-1 bg-gray-400 rounded-full"></div>
                    <div class="flex items-center gap-1.5 text-blue-600 font-medium">
                        <i class="fas fa-cloud text-sm"></i>
                        <span>Cloud Ready</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-7" x-data="{ isLoading: false }" @submit="isLoading = true">
            @csrf

            <!-- Email Field -->
            <div class="zenfleet-form-group">
                <label for="email" class="block text-sm font-bold text-gray-800 mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-envelope text-blue-600 text-sm"></i>
                        </div>
                        <span>Adresse Email Enterprise</span>
                        <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative group">
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email', 'admin@zenfleet.dz') }}"
                           required
                           autofocus
                           autocomplete="username"
                           placeholder="votre@entreprise.dz"
                           class="zenfleet-input-enterprise w-full pl-14 pr-4 py-4 text-base font-medium">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-at text-blue-500 text-lg group-focus-within:text-blue-600 transition-colors"></i>
                    </div>
                </div>
                @if($errors->get('email'))
                    <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-xl flex items-center gap-2 text-red-700">
                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                        <span class="text-sm font-medium">{{ $errors->first('email') }}</span>
                    </div>
                @endif
            </div>

            <!-- Password Field -->
            <div class="zenfleet-form-group">
                <label for="password" class="block text-sm font-bold text-gray-800 mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-purple-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-lock text-purple-600 text-sm"></i>
                        </div>
                        <span>Mot de Passe Sécurisé</span>
                        <span class="text-red-500">*</span>
                    </div>
                </label>
                <div class="relative group" x-data="{ showPassword: false }">
                    <input id="password"
                           :type="showPassword ? 'text' : 'password'"
                           name="password"
                           value="admin123"
                           required
                           autocomplete="current-password"
                           placeholder="••••••••••••••"
                           class="zenfleet-input-enterprise w-full pl-14 pr-14 py-4 text-base font-medium">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-key text-purple-500 text-lg group-focus-within:text-purple-600 transition-colors"></i>
                    </div>
                    <button type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-gray-700 transition-all hover:scale-110">
                        <i class="fas text-lg" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                @if($errors->get('password'))
                    <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-xl flex items-center gap-2 text-red-700">
                        <i class="fas fa-exclamation-triangle text-red-500"></i>
                        <span class="text-sm font-medium">{{ $errors->first('password') }}</span>
                    </div>
                @endif
            </div>

            <!-- Options & Recovery -->
            <div class="flex items-center justify-between mb-8">
                <label for="remember_me" class="flex items-center group cursor-pointer">
                    <div class="relative">
                        <input id="remember_me"
                               type="checkbox"
                               name="remember"
                               class="w-5 h-5 text-blue-600 bg-white border-2 border-gray-300 rounded-lg focus:ring-blue-500 focus:ring-2 transition-all">
                    </div>
                    <span class="ml-3 text-sm font-semibold text-gray-700 group-hover:text-gray-900 transition-colors">
                        Maintenir la session
                    </span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition-all flex items-center gap-1">
                        <i class="fas fa-key text-xs"></i>
                        Récupération
                    </a>
                @endif
            </div>

            <!-- Enterprise Login Button -->
            <button type="submit"
                    class="w-full group relative overflow-hidden rounded-2xl p-0.5 focus:outline-none focus:ring-4 focus:ring-blue-500/25 transition-all duration-300"
                    :disabled="isLoading">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 rounded-2xl"></div>
                <div class="relative px-8 py-4 bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 rounded-2xl text-white font-bold text-lg flex items-center justify-center gap-3 group-hover:from-blue-700 group-hover:via-purple-700 group-hover:to-indigo-700 transition-all duration-300 group-hover:scale-[1.02] group-active:scale-[0.98]">
                    <template x-if="!isLoading">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-fingerprint text-xl"></i>
                            <span>Accès Enterprise</span>
                        </div>
                    </template>
                    <template x-if="isLoading">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-spinner fa-spin text-xl"></i>
                            <span>Authentification...</span>
                        </div>
                    </template>
                </div>
            </button>

            <!-- Enterprise Info Panel -->
            <div class="mt-8 p-6 bg-gradient-to-br from-emerald-50 via-blue-50 to-purple-50 rounded-2xl border border-blue-200/50">
                <div class="text-center space-y-4">
                    <div class="flex items-center justify-center gap-2 text-emerald-600">
                        <i class="fas fa-shield-check text-xl"></i>
                        <span class="font-bold text-lg">Accès Sécurisé</span>
                    </div>

                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-blue-600">256</div>
                            <div class="text-xs text-gray-600 font-medium">Bits SSL</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-purple-600">24/7</div>
                            <div class="text-xs text-gray-600 font-medium">Support</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-emerald-600">99.9%</div>
                            <div class="text-xs text-gray-600 font-medium">Uptime</div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-blue-200/50">
                        <p class="text-sm text-gray-700 font-medium mb-3">Comptes Demo Enterprise</p>

                        <!-- Super Admin Account -->
                        <div class="bg-white/80 rounded-xl p-3 mb-3 border border-blue-200">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-6 h-6 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-crown text-white text-xs"></i>
                                </div>
                                <span class="text-xs font-bold text-gray-800">Super Admin</span>
                            </div>
                            <div class="space-y-1 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-600 w-16">Email:</span>
                                    <code class="px-2 py-1 bg-blue-50 rounded font-mono font-semibold text-blue-700">superadmin@zenfleet.dz</code>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-600 w-16">Password:</span>
                                    <code class="px-2 py-1 bg-purple-50 rounded font-mono font-semibold text-purple-700">ZenFleet2025!</code>
                                </div>
                            </div>
                        </div>

                        <!-- Simple Admin Account -->
                        <div class="bg-white/80 rounded-xl p-3 border border-green-200">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-6 h-6 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-shield text-white text-xs"></i>
                                </div>
                                <span class="text-xs font-bold text-gray-800">Admin Simple</span>
                            </div>
                            <div class="space-y-1 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-600 w-16">Email:</span>
                                    <code class="px-2 py-1 bg-green-50 rounded font-mono font-semibold text-green-700">admin@zenfleet.dz</code>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-600 w-16">Password:</span>
                                    <code class="px-2 py-1 bg-emerald-50 rounded font-mono font-semibold text-emerald-700">admin123</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (Route::has('register'))
                <!-- Enterprise Registration -->
                <div class="mt-6 text-center p-4 bg-gray-50 rounded-2xl">
                    <p class="text-sm text-gray-700">
                        <span class="font-medium">Organisation Enterprise ?</span>
                        <a href="{{ route('register') }}"
                           class="ml-2 font-bold text-blue-600 hover:text-blue-800 hover:underline transition-all">
                            Demander un accès →
                        </a>
                    </p>
                </div>
            @endif
        </form>
    </div>

    <!-- Enterprise Footer -->
    <div class="mt-10 text-center space-y-2">
        <div class="flex items-center justify-center gap-4 text-xs text-gray-500">
            <span class="flex items-center gap-1">
                <i class="fas fa-flag text-green-600"></i>
                Made in Algeria
            </span>
            <span>•</span>
            <span class="flex items-center gap-1">
                <i class="fas fa-shield-alt text-blue-600"></i>
                Enterprise Ready
            </span>
            <span>•</span>
            <span class="flex items-center gap-1">
                <i class="fas fa-cloud text-purple-600"></i>
                Cloud Native
            </span>
        </div>
        <p class="text-xs text-gray-500">
            © {{ date('Y') }} ZenFleet Enterprise. Plateforme certifiée de gestion de flotte.
        </p>
    </div>
</x-guest-layout>

