<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <meta name="csrf-token" content="{{ csrf_token() }}">
 <title>ZenFleet Enterprise - Connexion Sécurisée</title>
 
 <!-- Fonts -->
 <link rel="preconnect" href="https://fonts.bunny.net">
 <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />
 
 <!-- Scripts -->
 @vite(['resources/css/app.css', 'resources/js/app.js'])
 <script src="//unpkg.com/alpinejs" defer></script>
 
 <style>
 /* Variables de design système */
 :root {
 --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
 --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
 --gradient-success: linear-gradient(135deg, #0ba360 0%, #3cba92 100%);
 --gradient- linear-gradient(135deg, #1f2937 0%, #111827 100%);
 }

 /* Animations ultra-modernes */
 @keyframes float {
 0%, 100% { transform: translateY(0px) rotate(0deg); }
 33% { transform: translateY(-20px) rotate(-5deg); }
 66% { transform: translateY(20px) rotate(5deg); }
 }

 @keyframes gradientShift {
 0%, 100% { background-position: 0% 50%; }
 50% { background-position: 100% 50%; }
 }

 @keyframes pulse-glow {
 0%, 100% { box-shadow: 0 0 20px rgba(99, 102, 241, 0.5); }
 50% { box-shadow: 0 0 40px rgba(99, 102, 241, 0.8); }
 }

 @keyframes slideInUp {
 from {
 opacity: 0;
 transform: translateY(30px);
 }
 to {
 opacity: 1;
 transform: translateY(0);
 }
 }

 @keyframes morphing {
 0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; }
 50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; }
 }

 /* Background animé */
 .gradient-bg {
 background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #f5576c, #4facfe, #00f2fe);
 background-size: 400% 400%;
 animation: gradientShift 15s ease infinite;
 }

 /* Shapes flottants */
 .floating-shape {
 position: absolute;
 opacity: 0.1;
 animation: float 6s ease-in-out infinite;
 }

 .floating-shape:nth-child(1) {
 width: 80px;
 height: 80px;
 left: 10%;
 top: 20%;
 animation-delay: 0s;
 background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
 }

 .floating-shape:nth-child(2) {
 width: 120px;
 height: 120px;
 right: 10%;
 top: 40%;
 animation-delay: 2s;
 background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
 }

 .floating-shape:nth-child(3) {
 width: 60px;
 height: 60px;
 left: 20%;
 bottom: 20%;
 animation-delay: 4s;
 background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
 }

 /* Morphing blob */
 .morphing-shape {
 position: absolute;
 width: 400px;
 height: 400px;
 background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
 animation: morphing 8s ease-in-out infinite;
 opacity: 0.05;
 filter: blur(40px);
 }

 /* Glass card effect */
 .glass-card {
 background: rgba(255, 255, 255, 0.95);
 backdrop-filter: blur(20px) saturate(180%);
 -webkit-backdrop-filter: blur(20px) saturate(180%);
 border: 1px solid rgba(255, 255, 255, 0.3);
 box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
 }

 /* Input styles entreprise */
 .input-enterprise {
 background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(249, 250, 251, 0.95) 100%);
 border: 2px solid transparent;
 background-clip: padding-box;
 position: relative;
 transition: all 0.3s ease;
 }

 .input-enterprise::before {
 content: '';
 position: absolute;
 top: 0;
 left: 0;
 right: 0;
 bottom: 0;
 border-radius: 12px;
 padding: 2px;
 background: linear-gradient(135deg, #667eea, #764ba2);
 -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
 -webkit-mask-composite: xor;
 mask-composite: exclude;
 opacity: 0;
 transition: opacity 0.3s ease;
 }

 .input-enterprise:focus {
 box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
 }

 .input-enterprise:focus::before {
 opacity: 1;
 }

 /* Button entreprise */
 .btn-enterprise {
 position: relative;
 overflow: hidden;
 transition: all 0.3s ease;
 }

 .btn-enterprise::before {
 content: '';
 position: absolute;
 top: 50%;
 left: 50%;
 width: 0;
 height: 0;
 border-radius: 50%;
 background: rgba(255, 255, 255, 0.5);
 transform: translate(-50%, -50%);
 transition: width 0.6s, height 0.6s;
 }

 .btn-enterprise:hover::before {
 width: 300px;
 height: 300px;
 }

 /* Animations d'entrée */
 .animate-in {
 animation: slideInUp 0.6s ease-out;
 }

 .animate-in-delay-1 {
 animation: slideInUp 0.6s ease-out 0.1s both;
 }

 .animate-in-delay-2 {
 animation: slideInUp 0.6s ease-out 0.2s both;
 }

 .animate-in-delay-3 {
 animation: slideInUp 0.6s ease-out 0.3s both;
 }

 /* Custom scrollbar */
 ::-webkit-scrollbar {
 width: 8px;
 height: 8px;
 }

 ::-webkit-scrollbar-track {
 background: #f1f1f1;
 }

 ::-webkit-scrollbar-thumb {
 background: linear-gradient(135deg, #667eea, #764ba2);
 border-radius: 4px;
 }

 ::-webkit-scrollbar-thumb:hover {
 background: linear-gradient(135deg, #764ba2, #667eea);
 }
 </style>
</head>
<body class="font-sans antialiased gradient-bg min-h-screen">
 <!-- Shapes flottants pour effet dynamique -->
 <div class="floating-shape rounded-full"></div>
 <div class="floating-shape rounded-full"></div>
 <div class="floating-shape rounded-full"></div>
 <div class="morphing-shape top-0 right-0"></div>
 <div class="morphing-shape bottom-0 left-0"></div>

 <!-- Container principal -->
 <div class="min-h-screen flex flex-col justify-center items-center p-4">
 <!-- Card de connexion avec effet glass -->
 <div class="glass-card rounded-3xl w-full max-w-md p-8 lg:p-10 animate-in">
 
 <!-- Logo et titre -->
 <div class="text-center mb-8 animate-in-delay-1">
 <!-- Logo animé -->
 <div class="relative inline-block mb-6">
 <div class="w-24 h-24 mx-auto bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 rounded-3xl flex items-center justify-center shadow-2xl transform hover:scale-110 transition-transform duration-300 hover:rotate-3">
 <svg class="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
 </svg>
 </div>
 <!-- Badge Enterprise -->
 <span class="absolute -top-2 -right-2 bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg animate-pulse">
 ENTERPRISE
 </span>
 </div>

 <!-- Titre -->
 <h1 class="text-4xl lg:text-5xl font-black mb-2">
 <span class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
 ZenFleet
 </span>
 </h1>
 <p class="text-gray-600 font-medium">Plateforme de Gestion de Flotte</p>
 
 <!-- Indicateurs de sécurité -->
 <div class="flex items-center justify-center gap-4 mt-4">
 <div class="flex items-center gap-1 text-xs text-green-600 font-semibold">
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
 </svg>
 SSL 256-bit
 </div>
 <div class="flex items-center gap-1 text-xs text-blue-600 font-semibold">
 <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
 <path d="M10 2a5 5 0 00-5 5v2a2 2 0 00-2 2v5a2 2 0 002 2h10a2 2 0 002-2v-5a2 2 0 00-2-2H7V7a3 3 0 016 0v2h2V7a5 5 0 00-5-5z" />
 </svg>
 Connexion sécurisée
 </div>
 </div>
 </div>

 <!-- Formulaire -->
 <form method="POST" action="{{ route('login') }}" class="space-y-6 animate-in-delay-2" x-data="{ showPassword: false, loading: false }" @submit="loading = true">
 @csrf

 <!-- Email -->
 <div>
 <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
 Adresse Email
 </label>
 <div class="relative">
 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
 </svg>
 </div>
 <input id="email" 
 type="email" 
 name="email" 
 value="{{ old('email', 'admin@zenfleet.dz') }}" 
 required 
 autofocus
 class="input-enterprise block w-full pl-10 pr-3 py-3 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
 placeholder="votre@entreprise.dz">
 </div>
 @error('email')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <!-- Mot de passe -->
 <div>
 <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
 Mot de passe
 </label>
 <div class="relative">
 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
 <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
 </svg>
 </div>
 <input id="password" 
 :type="showPassword ? 'text' : 'password'" 
 name="password" 
 value="admin123"
 required
 class="input-enterprise block w-full pl-10 pr-10 py-3 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
 placeholder="••••••••">
 <button type="button" 
 @click="showPassword = !showPassword"
 class="absolute inset-y-0 right-0 pr-3 flex items-center">
 <svg x-show="!showPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
 </svg>
 <svg x-show="showPassword" x-cloak class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
 </svg>
 </button>
 </div>
 @error('password')
 <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
 @enderror
 </div>

 <!-- Remember me & Forgot password -->
 <div class="flex items-center justify-between">
 <label class="flex items-center">
 <input type="checkbox" 
 name="remember" 
 class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
 <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
 </label>
 @if (Route::has('password.request'))
 <a href="{{ route('password.request') }}" 
 class="text-sm text-indigo-600 hover:text-indigo-500 font-semibold">
 Mot de passe oublié?
 </a>
 @endif
 </div>

 <!-- Submit button -->
 <button type="submit" 
 :disabled="loading"
 class="btn-enterprise w-full flex justify-center items-center py-4 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 hover:from-indigo-700 hover:via-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform hover:scale-[1.02] transition-all duration-200">
 <span x-show="!loading" class="flex items-center gap-2">
 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
 </svg>
 Connexion Enterprise
 </span>
 <span x-show="loading" x-cloak class="flex items-center gap-2">
 <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
 <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
 <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
 </svg>
 Authentification...
 </span>
 </button>
 </form>

 <!-- Comptes de démonstration -->
 <div class="mt-8 p-4 bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 rounded-xl animate-in-delay-3">
 <p class="text-xs font-semibold text-gray-700 mb-3 text-center">COMPTES DE DÉMONSTRATION</p>
 
 <div class="space-y-2">
 <!-- Super Admin -->
 <div class="flex items-center justify-between p-2 bg-white/70 rounded-lg">
 <div class="flex items-center gap-2">
 <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full flex items-center justify-center">
 <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
 <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H6a2 2 0 100 4h2a2 2 0 100 4h2a1 1 0 100 2 2 2 0 01-2 2H4a2 2 0 01-2-2V7a2 2 0 012-2z" clip-rule="evenodd" />
 </svg>
 </div>
 <div>
 <p class="text-xs font-bold text-gray-800">Super Admin</p>
 <p class="text-xs text-gray-600">Accès total</p>
 </div>
 </div>
 <button type="button" 
 onclick="fillCredentials('superadmin@zenfleet.dz', 'ZenFleet2025!')"
 class="text-xs bg-indigo-100 text-indigo-700 px-3 py-1 rounded-lg font-semibold hover:bg-indigo-200 transition-colors">
 Utiliser
 </button>
 </div>

 <!-- Admin -->
 <div class="flex items-center justify-between p-2 bg-white/70 rounded-lg">
 <div class="flex items-center gap-2">
 <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
 <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
 </svg>
 </div>
 <div>
 <p class="text-xs font-bold text-gray-800">Admin</p>
 <p class="text-xs text-gray-600">Gestion standard</p>
 </div>
 </div>
 <button type="button" 
 onclick="fillCredentials('admin@zenfleet.dz', 'admin123')"
 class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-lg font-semibold hover:bg-green-200 transition-colors">
 Utiliser
 </button>
 </div>
 </div>
 </div>

 <!-- Footer -->
 <div class="mt-6 text-center text-xs text-gray-500">
 <p>© {{ date('Y') }} ZenFleet Enterprise. Tous droits réservés.</p>
 <div class="flex items-center justify-center gap-2 mt-2">
 <span class="flex items-center gap-1">
 <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
 </svg>
 99.9% Uptime
 </span>
 •
 <span>Support 24/7</span>
 •
 <span>ISO 27001</span>
 </div>
 </div>
 </div>
 </div>

 <script>
 function fillCredentials(email, password) {
 document.getElementById('email').value = email;
 document.getElementById('password').value = password;
 
 // Effet visuel
 document.getElementById('email').classList.add('ring-2', 'ring-indigo-500');
 document.getElementById('password').classList.add('ring-2', 'ring-indigo-500');
 
 setTimeout(() => {
 document.getElementById('email').classList.remove('ring-2', 'ring-indigo-500');
 document.getElementById('password').classList.remove('ring-2', 'ring-indigo-500');
 }, 1000);
 }
 </script>
</body>
</html>
