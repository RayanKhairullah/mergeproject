<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-mint-500 to-mint-600 dark:from-mint-600 dark:to-mint-700 rounded-lg p-8 text-white">
        <h1 class="text-3xl font-bold tracking-tighter text-balance mb-2">
            Selamat Datang, {{ auth()->user()->name }}!
        </h1>
        <p class="text-mint-100">
            Akses cepat ke semua fitur yang tersedia untuk Anda
        </p>
    </div>

    <!-- Quick Access Cards -->
    <div>
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Akses Cepat</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Vehicle Monitor -->
            <a href="{{ route('vehicles.monitor') }}" 
               class="group relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 hover:border-mint-500 dark:hover:border-mint-500 transition-all duration-300 hover:shadow-lg">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-mint-100 dark:bg-mint-900/30 flex items-center justify-center group-hover:bg-mint-500 transition-colors">
                        <flux:icon.computer-desktop class="w-6 h-6 text-mint-600 dark:text-mint-400 group-hover:text-white" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Monitor Mobil</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Lihat status kendaraan secara real-time</p>
                    </div>
                </div>
            </a>

            <!-- Meeting Monitor -->
            <a href="{{ route('meetings.monitor') }}" 
               class="group relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 hover:border-mint-500 dark:hover:border-mint-500 transition-all duration-300 hover:shadow-lg">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-mint-100 dark:bg-mint-900/30 flex items-center justify-center group-hover:bg-mint-500 transition-colors">
                        <flux:icon.tv class="w-6 h-6 text-mint-600 dark:text-mint-400 group-hover:text-white" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Monitor Rapat</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Pantau jadwal rapat yang sedang berlangsung</p>
                    </div>
                </div>
            </a>

            <!-- Digital Library -->
            <a href="{{ route('books.index') }}" 
               class="group relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 hover:border-mint-500 dark:hover:border-mint-500 transition-all duration-300 hover:shadow-lg">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-mint-100 dark:bg-mint-900/30 flex items-center justify-center group-hover:bg-mint-500 transition-colors">
                        <flux:icon.book-open class="w-6 h-6 text-mint-600 dark:text-mint-400 group-hover:text-white" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Digital Library</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Akses koleksi buku digital</p>
                    </div>
                </div>
            </a>

            <!-- Vehicle Loan -->
            <a href="{{ route('vehicles.loan') }}" 
               class="group relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 hover:border-mint-500 dark:hover:border-mint-500 transition-all duration-300 hover:shadow-lg">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-mint-100 dark:bg-mint-900/30 flex items-center justify-center group-hover:bg-mint-500 transition-colors">
                        <flux:icon.arrow-up-tray class="w-6 h-6 text-mint-600 dark:text-mint-400 group-hover:text-white" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Peminjaman Kendaraan</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Form peminjaman kendaraan</p>
                    </div>
                </div>
            </a>

            <!-- Vehicle Return -->
            <a href="{{ route('vehicles.return') }}" 
               class="group relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 hover:border-mint-500 dark:hover:border-mint-500 transition-all duration-300 hover:shadow-lg">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-mint-100 dark:bg-mint-900/30 flex items-center justify-center group-hover:bg-mint-500 transition-colors">
                        <flux:icon.arrow-down-tray class="w-6 h-6 text-mint-600 dark:text-mint-400 group-hover:text-white" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Pengembalian Kendaraan</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Form pengembalian kendaraan</p>
                    </div>
                </div>
            </a>

            <!-- Vehicle Expense -->
            <a href="{{ route('vehicles.expense') }}" 
               class="group relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 hover:border-mint-500 dark:hover:border-mint-500 transition-all duration-300 hover:shadow-lg">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-mint-100 dark:bg-mint-900/30 flex items-center justify-center group-hover:bg-mint-500 transition-colors">
                        <flux:icon.currency-dollar class="w-6 h-6 text-mint-600 dark:text-mint-400 group-hover:text-white" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Rupa-rupa</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Laporan biaya kendaraan</p>
                    </div>
                </div>
            </a>

            <!-- Vehicle Inspection (Only for users with access dashboard permission) -->
            @can('access dashboard')
                <a href="{{ route('vehicles.inspection') }}" 
                   class="group relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 hover:border-mint-500 dark:hover:border-mint-500 transition-all duration-300 hover:shadow-lg">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-mint-100 dark:bg-mint-900/30 flex items-center justify-center group-hover:bg-mint-500 transition-colors">
                            <flux:icon.clipboard-document-check class="w-6 h-6 text-mint-600 dark:text-mint-400 group-hover:text-white" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Kesiapan Mobil</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Form inspeksi kesiapan kendaraan</p>
                        </div>
                    </div>
                </a>
            @endcan

        </div>
    </div>

    <!-- Panel Administrasi (Super Admin & Admin) -->
    @can('access dashboard')
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Panel Administrasi</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Admin Dashboard -->
                <a href="{{ route('admin.index') }}" 
                   class="group relative overflow-hidden rounded-lg border-2 border-mint-500 dark:border-mint-600 bg-gradient-to-br from-mint-50 to-mint-100 dark:from-mint-900/20 dark:to-mint-800/20 p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-mint-500 flex items-center justify-center">
                            <flux:icon.cog-6-tooth class="w-6 h-6 text-white" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Admin Dashboard</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Akses panel administrasi lengkap</p>
                        </div>
                    </div>
                </a>

            </div>
        </div>
    @endcan

    <!-- Panel SDM (SDM Role) -->
    @role('sdm')
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Panel SDM</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Meeting Management -->
                <a href="{{ route('admin.meetings.index') }}" 
                   class="group relative overflow-hidden rounded-lg border-2 border-mint-500 dark:border-mint-600 bg-gradient-to-br from-mint-50 to-mint-100 dark:from-mint-900/20 dark:to-mint-800/20 p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-mint-500 flex items-center justify-center">
                            <flux:icon.calendar class="w-6 h-6 text-white" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Kelola Rapat</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Manajemen dan approval permintaan rapat</p>
                        </div>
                    </div>
                </a>

                <!-- Banquet Management -->
                <a href="{{ route('admin.banquets.index') }}" 
                   class="group relative overflow-hidden rounded-lg border-2 border-mint-500 dark:border-mint-600 bg-gradient-to-br from-mint-50 to-mint-100 dark:from-mint-900/20 dark:to-mint-800/20 p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-mint-500 flex items-center justify-center">
                            <flux:icon.cake class="w-6 h-6 text-white" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Kelola Banquet</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Manajemen dan approval permintaan banquet</p>
                        </div>
                    </div>
                </a>

            </div>
        </div>
    @endrole

    <!-- Meeting & Banquet (User Role) -->
    @role('user')
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Meeting & Banquet</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Meeting Request -->
                <a href="{{ route('admin.meetings.index') }}" 
                   class="group relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 hover:border-mint-500 dark:hover:border-mint-500 transition-all duration-300 hover:shadow-lg">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-mint-100 dark:bg-mint-900/30 flex items-center justify-center group-hover:bg-mint-500 transition-colors">
                            <flux:icon.calendar class="w-6 h-6 text-mint-600 dark:text-mint-400 group-hover:text-white" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Permintaan Rapat</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Ajukan permintaan ruang rapat</p>
                        </div>
                    </div>
                </a>

                <!-- Banquet Request -->
                <a href="{{ route('admin.banquets.index') }}" 
                   class="group relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 hover:border-mint-500 dark:hover:border-mint-500 transition-all duration-300 hover:shadow-lg">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-mint-100 dark:bg-mint-900/30 flex items-center justify-center group-hover:bg-mint-500 transition-colors">
                            <flux:icon.cake class="w-6 h-6 text-mint-600 dark:text-mint-400 group-hover:text-white" />
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Permintaan Banquet</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Ajukan permintaan banquet</p>
                        </div>
                    </div>
                </a>

            </div>
        </div>
    @endrole

    <!-- Settings -->
    <div>
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Pengaturan</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Profile Settings -->
            <a href="{{ route('settings.profile') }}" 
               class="group relative overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 hover:border-mint-500 dark:hover:border-mint-500 transition-all duration-300 hover:shadow-lg">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center group-hover:bg-mint-500 transition-colors">
                        <flux:icon.user class="w-6 h-6 text-gray-600 dark:text-gray-400 group-hover:text-white" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Profil</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Kelola informasi profil Anda</p>
                    </div>
                </div>
            </a>

        </div>
    </div>
</div>
