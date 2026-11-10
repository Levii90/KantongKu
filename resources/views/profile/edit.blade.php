<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="pb-4 border-b border-slate-800/80">
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Pengaturan Profil</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Kelola data informasi akun, keamanan password, dan preferensi akun Anda.</p>
        </div>

        <!-- Profile Information -->
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-6 sm:p-8 backdrop-blur-md shadow-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Update Password -->
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-6 sm:p-8 backdrop-blur-md shadow-lg">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Delete Account -->
        <div class="rounded-2xl border border-rose-500/20 bg-rose-950/10 p-6 sm:p-8 backdrop-blur-md shadow-lg">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
