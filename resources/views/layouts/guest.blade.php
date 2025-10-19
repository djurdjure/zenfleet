<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <meta name="csrf-token" content="{{ csrf_token() }}">

 <title>{{ config('app.name', 'ZenFleet') }} - Plateforme Enterprise</title>

 <!-- Fonts -->
 <link rel="preconnect" href="https://fonts.googleapis.com">
 <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
 <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

 <!-- Font Awesome Pro -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

 <!-- Production Assets -->
 @vite(['resources/js/app.js'])

 <style>
 .enterprise-bg {
 background:
 radial-gradient(circle at 20% 20%, rgba(30, 64, 175, 0.15) 0%, transparent 50%),
 radial-gradient(circle at 80% 20%, rgba(79, 70, 229, 0.15) 0%, transparent 50%),
 radial-gradient(circle at 40% 80%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
 linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #f1f5f9 100%);
 }

 .enterprise-pattern::before {
 content: '';
 position: absolute;
 inset: 0;
 background-image:
 radial-gradient(circle at 2px 2px, rgba(30, 64, 175, 0.15) 1px, transparent 0);
 background-size: 24px 24px;
 opacity: 0.3;
 }

 .glass-morphism {
 backdrop-filter: blur(16px) saturate(180%);
 background: rgba(255, 255, 255, 0.85);
 border: 1px solid rgba(255, 255, 255, 0.125);
 }
 </style>
</head>
<body class="font-sans antialiased bg-gray-50">
 <!-- 🚀 ZenFleet Enterprise Login Experience -->
 <div class="min-h-screen enterprise-bg enterprise-pattern flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">

 <!-- Floating Elements -->
 <div class="absolute top-20 left-20 w-64 h-64 bg-gradient-to-br from-blue-400/20 to-purple-600/20 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse"></div>
 <div class="absolute bottom-20 right-20 w-64 h-64 bg-gradient-to-br from-purple-400/20 to-pink-600/20 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse animation-delay-2000"></div>
 <div class="absolute top-1/2 left-1/3 w-32 h-32 bg-gradient-to-br from-green-400/20 to-blue-600/20 rounded-full mix-blend-multiply filter blur-xl opacity-50 animate-pulse animation-delay-4000"></div>

 <!-- Main Login Container -->
 <div class="relative w-full max-w-md">
 <!-- Background Glow -->
 <div class="absolute -inset-4 bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 rounded-3xl blur-xl opacity-25 animate-pulse"></div>

 <!-- Login Card -->
 <div class="relative glass-morphism rounded-3xl p-8 sm:p-10 shadow-2xl">
 {{ $slot }}
 </div>
 </div>
 </div>

 <!-- Alpine.js -->
 <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

 <!-- Enterprise Animations -->
 <style>
 .animation-delay-2000 { animation-delay: 2s; }
 .animation-delay-4000 { animation-delay: 4s; }

 @keyframes float {
 0%, 100% { transform: translateY(0px); }
 50% { transform: translateY(-20px); }
 }

 .float { animation: float 6s ease-in-out infinite; }
 </style>
</body>
</html>

