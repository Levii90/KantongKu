<x-guest-layout>
    <div class="w-full min-h-screen md:grid md:grid-cols-2" x-data="{
        showPassword: false,
        isSubmitting: false,
        typewriterText: '',
        textIndex: 0,
        texts: [
            'Create an account. A new chapter awaits.',
            'Mulai langkah pertama menuju kebebasan finansial.',
            'Catat pemasukan dan pengeluaran dengan lebih bijak.'
        ],
        charIndex: 0,
        isDeleting: false,
        init() {
            this.tick();
        },
        tick() {
            const current = this.texts[this.textIndex];
            if (this.isDeleting) {
                this.typewriterText = current.substring(0, this.charIndex - 1);
                this.charIndex--;
            } else {
                this.typewriterText = current.substring(0, this.charIndex + 1);
                this.charIndex++;
            }

            let delay = this.isDeleting ? 35 : 70;
            if (!this.isDeleting && this.charIndex === current.length) {
                delay = 2200;
                this.isDeleting = true;
            } else if (this.isDeleting && this.charIndex === 0) {
                this.isDeleting = false;
                this.textIndex = (this.textIndex + 1) % this.texts.length;
                delay = 500;
            }
            setTimeout(() => this.tick(), delay);
        }
    }">
        <style>
            input[type="password"]::-ms-reveal,
            input[type="password"]::-ms-clear {
                display: none;
            }
        </style>

        <!-- Left Column: Registration Form -->
        <div class="flex min-h-screen items-center justify-center p-6 md:p-12 bg-transparent">
            <div class="mx-auto grid w-full max-w-[380px] gap-6">
                <!-- Brand Header -->
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-white">Kantong<span class="text-indigo-400">Ku</span></span>
                </div>

                <div class="flex flex-col gap-1.5">
                    <h1 class="text-2xl font-bold tracking-tight text-white">Create an account</h1>
                    <p class="text-sm text-slate-400">Enter your details below to create your account</p>
                </div>

                <form method="POST" action="{{ route('register') }}" @submit="isSubmitting = true" class="flex flex-col gap-4">
                    @csrf

                    <!-- Name Input -->
                    <div class="grid gap-2">
                        <label for="name" class="text-sm font-medium text-slate-300">Full Name</label>
                        <input id="name"
                               type="text"
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="John Doe"
                               required
                               autofocus
                               autocomplete="name"
                               class="flex h-11 w-full rounded-lg border @error('name') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @else border-slate-800 focus:border-indigo-500 focus:ring-indigo-500/20 @enderror bg-slate-900/80 px-3.5 py-2 text-sm text-slate-100 shadow-sm transition placeholder:text-slate-500 focus:outline-none focus:ring-2 disabled:opacity-50">
                        @error('name')
                            <p class="text-xs text-rose-400 font-medium flex items-center gap-1.5 mt-0.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Email Input -->
                    <div class="grid gap-2">
                        <label for="email" class="text-sm font-medium text-slate-300">Email</label>
                        <input id="email"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="m@example.com"
                               required
                               autocomplete="email"
                               class="flex h-11 w-full rounded-lg border @error('email') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @else border-slate-800 focus:border-indigo-500 focus:ring-indigo-500/20 @enderror bg-slate-900/80 px-3.5 py-2 text-sm text-slate-100 shadow-sm transition placeholder:text-slate-500 focus:outline-none focus:ring-2 disabled:opacity-50">
                        @error('email')
                            <p class="text-xs text-rose-400 font-medium flex items-center gap-1.5 mt-0.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Password Input with Toggle -->
                    <div class="grid gap-2">
                        <label for="password" class="text-sm font-medium text-slate-300">Password</label>
                        <div class="relative">
                            <input id="password"
                                   :type="showPassword ? 'text' : 'password'"
                                   name="password"
                                   required
                                   autocomplete="new-password"
                                   placeholder="Min. 8 characters"
                                   class="flex h-11 w-full rounded-lg border @error('password') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @else border-slate-800 focus:border-indigo-500 focus:ring-indigo-500/20 @enderror bg-slate-900/80 px-3.5 pe-11 py-2 text-sm text-slate-100 shadow-sm transition placeholder:text-slate-500 focus:outline-none focus:ring-2 disabled:opacity-50">
                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 end-0 flex h-full w-10 items-center justify-center text-slate-400 hover:text-slate-200 transition focus:outline-none">
                                <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-xs text-rose-400 font-medium flex items-center gap-1.5 mt-0.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Confirm Password Input -->
                    <div class="grid gap-2">
                        <label for="password_confirmation" class="text-sm font-medium text-slate-300">Confirm Password</label>
                        <input id="password_confirmation"
                               :type="showPassword ? 'text' : 'password'"
                               name="password_confirmation"
                               required
                               autocomplete="new-password"
                               placeholder="Repeat password"
                               class="flex h-11 w-full rounded-lg border @error('password_confirmation') border-rose-500 @else border-slate-800 @enderror bg-slate-900/80 px-3.5 py-2 text-sm text-slate-100 shadow-sm transition placeholder:text-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 disabled:opacity-50">
                        @error('password_confirmation')
                            <p class="text-xs text-rose-400 font-medium flex items-center gap-1.5 mt-0.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            :disabled="isSubmitting"
                            class="inline-flex items-center justify-center gap-2 h-11 w-full rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold shadow-lg shadow-indigo-600/20 transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500/40 disabled:opacity-60 disabled:cursor-not-allowed mt-2">
                        <svg x-show="isSubmitting" x-cloak class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isSubmitting ? 'Creating account...' : 'Create Account'">Create Account</span>
                    </button>
                </form>

                <!-- Switch Link -->
                <div class="text-center text-sm text-slate-400">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-medium text-indigo-400 hover:text-indigo-300 ml-1 transition-colors">
                        Sign in
                    </a>
                </div>

                <!-- Divider -->
                <div class="relative text-center text-xs after:absolute after:inset-0 after:top-1/2 after:z-0 after:flex after:items-center after:border-t after:border-slate-800">
                    <span class="relative z-10 bg-slate-950 px-3 text-slate-500 uppercase tracking-wider font-medium">Or continue with</span>
                </div>

                <!-- Google SSO Mock Button -->
                <button type="button"
                        class="inline-flex items-center justify-center gap-2.5 h-11 w-full rounded-lg border border-slate-800 bg-slate-900/60 hover:bg-slate-800/80 text-sm font-medium text-slate-200 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-700">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.6l3.1-3.1C17.3 1.7 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.3 9 5 12 5z"/>
                        <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.7-.2-2.3H12v4.5h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.8z"/>
                        <path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.8s.2-2.1.4-2.8L1.9 6.3C.7 8.7 0 10.3 0 12s.7 3.3 1.9 5.7l3.7-2.9z"/>
                        <path fill="#34A853" d="M12 23c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.3-6.4-5.2L1.9 16C3.7 19.7 7.5 23 12 23z"/>
                    </svg>
                    <span>Continue with Google</span>
                </button>
            </div>
        </div>

        <!-- Right Column: Visual & Typewriter Quote -->
        <div class="hidden md:block relative bg-cover bg-center transition-all duration-500 ease-in-out"
             style="background-image: url('https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?q=80&w=1200&auto=format&fit=crop');">
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-slate-950/20"></div>

            <!-- Glowing Ridge Accent -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-violet-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex h-full flex-col items-center justify-end p-8 pb-14">
                <blockquote class="space-y-3 text-center text-slate-100 max-w-md bg-slate-950/70 p-6 rounded-2xl backdrop-blur-md border border-slate-800/80 shadow-2xl">
                    <p class="text-lg font-medium leading-relaxed tracking-tight text-slate-200">
                        “<span x-text="typewriterText"></span><span class="animate-pulse text-indigo-400">|</span>”
                    </p>
                    <cite class="block text-xs font-medium text-slate-400 not-italic tracking-wide uppercase">
                        — KantongKu Financial Companion
                    </cite>
                </blockquote>
            </div>
        </div>
    </div>
</x-guest-layout>
