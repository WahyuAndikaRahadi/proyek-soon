<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Jurnal Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6; /* Tailwind's gray-100 for a soft background */
        }

        .input-custom-focus:focus {
            border-color: #4f46e5; /* Tailwind's indigo-600 */
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.3); /* Adjusted to indigo-600 */
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #e5e7eb; } /* gray-200 */
        ::-webkit-scrollbar-thumb { background: #9ca3af; border-radius: 3px;} /* gray-400 */
        ::-webkit-scrollbar-thumb:hover { background: #6b7280; } /* gray-500 */

        .input-icon-wrapper .input-icon { transition: color 0.2s ease-in-out; }
        /* CSS focus for icon color (fallback/enhancement) */
        .input-group:focus-within .input-icon-wrapper .input-icon {
            color: #4f46e5; /* indigo-600 */
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 selection:bg-purple-500 selection:text-white">

    <div id="login-wrapper" class="w-full max-w-xs sm:max-w-md lg:max-w-4xl">
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden lg:flex">

            <div id="branding-panel" class="lg:w-5/12 bg-gradient-to-br from-purple-600 via-indigo-600 to-blue-700 text-white p-6 py-8 sm:p-8 md:p-10 lg:p-12 flex flex-col justify-center items-center text-center">
                <div class="mb-4 sm:mb-6">
                    <img src="https://res.cloudinary.com/dxbkwpm3i/image/upload/v1748678502/59619828logo69-600x750_-_Edited_raug9b.png"
                         alt="Logo SMK Negeri 69 Jakarta"
                         class="w-20 sm:w-24 md:w-28 mx-auto"
                         draggable="false"
                         oncontextmenu="return false;">
                </div>
                <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold mb-1 sm:mb-2">SMK Negeri 69 Jakarta</h1>
                <p class="text-xs sm:text-sm md:text-base text-indigo-100 italic">"Berbudaya, Berkarakter, dan Berkarya"</p>
                <p class="text-xs text-indigo-200 mt-6 sm:mt-8 hidden lg:block">Selamat datang di sistem Jurnal Sekolah. Silakan login untuk melanjutkan.</p>
            </div>

            <div id="form-panel" class="lg:w-7/12 p-6 sm:p-8 md:p-10">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold text-gray-800 text-center lg:text-left mb-1">Login Jurnal Sekolah</h2>
                <p class="text-xs sm:text-sm text-gray-500 text-center lg:text-left mb-5 sm:mb-6 md:mb-8">Masukkan akun terdaftar Anda untuk mengakses website.</p>

                @if ($errors->any())
                    <div id="error-alert" class="mb-5 p-3 sm:p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-md shadow-sm">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 pt-0.5">
                                <i class="fas fa-exclamation-circle text-red-500"></i>
                            </div>
                            <div class="ml-2 sm:ml-3">
                                <h3 class="text-xs sm:text-sm font-medium">Terjadi kesalahan</h3>
                                <div class="mt-1 text-xs sm:text-sm">
                                    <ul class="list-disc pl-4 sm:pl-5 space-y-0.5 sm:space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="post" class="space-y-4 sm:space-y-5">
                    @csrf

                    <div id="email-field">
                        <label for="email" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Email</label>
                        <div class="relative rounded-md input-group">
                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-3.5 flex items-center pointer-events-none input-icon-wrapper">
                                <i class="fas fa-envelope text-gray-400 text-xs sm:text-sm input-icon"></i>
                            </div>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                   class="input-custom-focus block w-full pl-8 sm:pl-10 pr-3 py-2 sm:py-2.5 border border-gray-300 rounded-md focus:outline-none transition duration-200 placeholder-gray-400 text-gray-900 text-sm sm:text-base"
                                   placeholder="email@smkn69.jkt" required autofocus>
                        </div>
                        @error('email')
                            <p class="mt-1 sm:mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="password-field">
                        <label for="password" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative rounded-md input-group">
                            <div class="absolute inset-y-0 left-0 pl-3 sm:pl-3.5 flex items-center pointer-events-none input-icon-wrapper">
                                <i class="fas fa-lock text-gray-400 text-xs sm:text-sm input-icon"></i>
                            </div>
                            <input type="password" name="password" id="password"
                                   class="input-custom-focus block w-full pl-8 sm:pl-10 pr-10 py-2 sm:py-2.5 border border-gray-300 rounded-md focus:outline-none transition duration-200 placeholder-gray-400 text-gray-900 text-sm sm:text-base"
                                   placeholder="••••••••" required>
                            <div class="absolute inset-y-0 right-0 pr-3 sm:pr-3.5 flex items-center">
                                <button type="button" id="toggle-password"
                                        class="text-gray-500 hover:text-indigo-600 focus:outline-none focus:text-indigo-600"
                                        aria-label="Toggle password visibility">
                                    <i id="eye-icon" class="fas fa-eye text-xs sm:text-sm"></i>
                                </button>
                            </div>
                        </div>
                        @error('password')
                            <p class="mt-1 sm:mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="remember-field" class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                                   class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded transition">
                            <label for="remember" class="ml-2 block text-xs sm:text-sm text-gray-700">Ingat Saya</label>
                        </div>
                        {{-- Kondisi Route::has() akan dievaluasi oleh Laravel.
                             Untuk demo statis, link ini akan selalu muncul jika tidak ada @if.
                             Jika Anda ingin menyembunyikannya secara default di demo statis,
                             Anda bisa membungkusnya dengan @if(false) atau menghapusnya.
                             Namun, karena ini template Blade, kita biarkan agar sesuai dengan lingkungan Laravel. --}}
                        @if (Route::has('password.request'))
                            <div class="text-xs sm:text-sm">
                                <a href="{{ route('password.request') }}" class="font-medium text-indigo-600 hover:text-indigo-500 transition duration-200">
                                    Lupa password?
                                </a>
                            </div>
                        @else
                             <div class="text-xs sm:text-sm">
                                <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500 transition duration-200">
                                    Lupa password?
                                </a>
                            </div>
                        @endif
                    </div>

                    <button type="submit" id="login-button"
                            class="w-full flex justify-center py-2.5 sm:py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200 ease-in-out active:bg-indigo-800">
                        <span class="flex items-center">
                            <i class="fas fa-sign-in-alt mr-2 text-xs sm:text-sm"></i>
                            Masuk
                        </span>
                    </button>
                </form>

                <div class="mt-6 sm:mt-8 text-center text-xs text-gray-500">
                    <p>&copy; {{ date('Y') }} Jurnal SMK Negeri 69 Jakarta. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/motion@latest/dist/motion.min.js"></script>
    <script>
        // Pastikan Motion diekspor dengan benar, jika tidak, coba window.Motion
        const { animate, stagger } = Motion || window.Motion;

        document.addEventListener('DOMContentLoaded', function() {
            // Main wrapper animation
            animate('#login-wrapper',
                { opacity: [0, 1], scale: [0.97, 1] }, // Slightly more subtle scale
                { duration: 0.5, ease: "ease-out" }
            );

            // Split panel animation for desktop
            if (window.innerWidth >= 1024) { // lg breakpoint (1024px)
                animate('#branding-panel',
                    { opacity: [0, 1], x: [-40, 0] }, // Increased x offset slightly
                    { duration: 0.6, delay: 0.15, ease: "ease-out" }
                );

                animate('#form-panel',
                    { opacity: [0, 1], x: [40, 0] }, // Increased x offset slightly
                    { duration: 0.6, delay: 0.15, ease: "ease-out" }
                );
            }

            // Form elements animation
            const formElementSelectors = [
                '#form-panel h2',
                '#form-panel > p:not(.text-xs)', // Target direct child p, exclude footer
                '#email-field',
                '#password-field',
                '#remember-field',
                '#login-button'
            ];
            const formElements = document.querySelectorAll(formElementSelectors.join(', '));

            if (formElements.length > 0) {
                animate(formElements,
                    { opacity: [0, 1], y: [15, 0] }, // Increased y offset slightly
                    { delay: stagger(0.07, { startDelay: window.innerWidth >= 1024 ? 0.35 : 0.2 }), duration: 0.4, ease: "ease-out" }
                );
            }


            // Error alert animation if present
            const errorAlert = document.getElementById('error-alert');
            if (errorAlert) {
                animate(errorAlert,
                    { opacity: [0, 1], y: [-10, 0], scale: [0.95, 1] },
                    { duration: 0.4, delay: (window.innerWidth >= 1024 ? 0.5 : 0.3), ease: "ease-out" }
                );
            }

            // --- Password toggle functionality ---
            const passwordInput = document.getElementById('password'); // Changed variable name for clarity
            const togglePasswordButton = document.getElementById('toggle-password');
            const eyeIcon = document.getElementById('eye-icon');

            if (togglePasswordButton && passwordInput && eyeIcon) {
                togglePasswordButton.addEventListener('click', function() {
                    const isPasswordVisible = passwordInput.type === 'text';
                    passwordInput.type = isPasswordVisible ? 'password' : 'text';

                    // Toggle eye icon classes
                    eyeIcon.classList.toggle('fa-eye', isPasswordVisible);
                    eyeIcon.classList.toggle('fa-eye-slash', !isPasswordVisible);

                    // Animate the icon change
                    animate(eyeIcon,
                        { scale: [1, 1.3, 1], opacity: [0.6, 1]}, // More pronounced scale
                        { duration: 0.25 } // Slightly faster
                    );
                });
            }

            // Login button interaction effects
            const loginButton = document.getElementById('login-button');
            if (loginButton) {
                loginButton.addEventListener('mouseenter', () => {
                    animate(loginButton, { scale: 1.03, backgroundColor: "#4338ca" /* indigo-700 */ }, { duration: 0.15 });
                });
                loginButton.addEventListener('mouseleave', () => {
                    animate(loginButton, { scale: 1, backgroundColor: "#4f46e5" /* indigo-600 */}, { duration: 0.15 });
                });
                loginButton.addEventListener('mousedown', () => {
                    animate(loginButton, { scale: 0.97, backgroundColor: "#3730a3" /* indigo-800 */ }, { duration: 0.1 });
                });
                loginButton.addEventListener('mouseup', () => {
                    const finalScale = loginButton.matches(':hover') ? 1.03 : 1;
                    const finalBg = loginButton.matches(':hover') ? "#4338ca" : "#4f46e5";
                    animate(loginButton, { scale: finalScale, backgroundColor: finalBg }, { duration: 0.1 });
                });
            }

            // Input focus/blur effects for icons (using Motion One)
            const inputsWithIcons = document.querySelectorAll('#email-field input, #password-field input');
            inputsWithIcons.forEach(input => {
                const iconWrapper = input.previousElementSibling; // Assuming icon is in a sibling div before input
                if (iconWrapper && iconWrapper.querySelector('.input-icon')) {
                    const icon = iconWrapper.querySelector('.input-icon');
                    input.addEventListener('focus', () => {
                        animate(icon, { color: "#4f46e5" /* indigo-600 */ }, { duration: 0.2 });
                    });
                    input.addEventListener('blur', () => {
                        // Only revert color if input is empty or not in error state, or simply always revert
                        animate(icon, { color: "#9ca3af" /* gray-400 */ }, { duration: 0.2 });
                    });
                }
            });
        });
    </script>
</body>
</html>
