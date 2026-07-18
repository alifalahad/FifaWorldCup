@extends('layouts.app')

@section('title', 'Welcome to FIFA WC Manager')

@section('content')
<div class="relative bg-gray-900 overflow-hidden">
    <!-- Background overlay / pattern -->
    <div class="absolute inset-0 z-0 opacity-20">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-800 to-purple-900 mix-blend-multiply"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-24 sm:pb-32 lg:pt-32 lg:pb-40">
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-4xl tracking-tight font-extrabold text-white sm:text-5xl md:text-6xl drop-shadow-lg">
                <span class="block mb-2">The Ultimate</span>
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-300">FIFA World Cup Manager</span>
            </h1>
            <p class="mt-6 max-w-md mx-auto text-base text-gray-300 sm:text-lg md:mt-8 md:text-xl md:max-w-2xl drop-shadow">
                Explore historical tournament data, track real-time match results, follow your favorite teams, and dive deep into player statistics.
            </p>
            <div class="mt-10 max-w-sm mx-auto sm:max-w-none sm:flex sm:justify-center gap-4">
                <a href="{{ route('tournaments.index') }}" class="group relative flex items-center justify-center w-full sm:w-auto px-8 py-3 text-base font-medium rounded-full text-white bg-indigo-600 hover:bg-indigo-500 hover:shadow-xl hover:shadow-indigo-500/30 transition-all duration-300 transform hover:-translate-y-1">
                    Explore Tournaments
                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </a>
                @guest
                <a href="{{ route('register') }}" class="mt-4 sm:mt-0 flex items-center justify-center w-full sm:w-auto px-8 py-3 text-base font-medium rounded-full text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm border border-white/20 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    Join Now
                </a>
                @endguest
            </div>
        </div>
    </div>
    
    <!-- Decorative bottom wave -->
    <div class="absolute bottom-0 inset-x-0">
        <svg class="w-full h-12 text-gray-100 fill-current" viewBox="0 0 1440 48" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,48 L1440,48 L1440,0 C1120,48 720,24 360,36 C120,44 0,0 0,0 Z"></path>
        </svg>
    </div>
</div>

<div class="bg-gray-100 py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Everything you need</h2>
            <p class="mt-4 max-w-2xl text-lg text-gray-500 mx-auto">Comprehensive data for the world's biggest sporting event.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-indigo-100 transition-all duration-300">
                    <span class="text-2xl">🏆</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Tournaments</h3>
                <p class="text-gray-600 leading-relaxed">View all historical World Cups, from group stages to the dramatic finals. Complete bracket tracking and host information.</p>
            </div>
            
            <!-- Feature 2 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-emerald-100 transition-all duration-300">
                    <span class="text-2xl">⚽</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Matches & Results</h3>
                <p class="text-gray-600 leading-relaxed">Detailed match reports, goal scorers, cards, and referee assignments. Live updates and historical archives.</p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-rose-100 transition-all duration-300">
                    <span class="text-2xl">🌍</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Teams & Players</h3>
                <p class="text-gray-600 leading-relaxed">Comprehensive rosters, player statistics, global rankings, and confederation details for every participating nation.</p>
            </div>
        </div>
    </div>
</div>
@endsection
