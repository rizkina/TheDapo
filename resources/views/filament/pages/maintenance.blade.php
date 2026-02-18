<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- KARTU STATUS KONEKSI -->
        <x-filament::section>
            <x-slot name="heading">Sistem Health Check</x-slot>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-900">
                    <span class="text-sm font-medium">Google Drive API</span>
                    <x-filament::badge :color="$driveStatus['success'] ? 'success' : 'danger'">
                        {{ $driveStatus['success'] ? 'Terhubung' : 'Terputus' }}
                    </x-filament::badge>
                </div>

                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-900">
                    <span class="text-sm font-medium">Dapodik Web Service</span>
                    <x-filament::badge :color="$dapoStatus['success'] ? 'success' : 'danger'">
                        {{ $dapoStatus['success'] ? 'Online' : 'Offline' }}
                    </x-filament::badge>
                </div>
            </div>
        </x-filament::section>

        <!-- KARTU RINGKASAN DATA -->
        <x-filament::section>
            <x-slot name="heading">Ringkasan Database</x-slot>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Card Siswa -->
                    <div class="flex flex-col items-center justify-center p-4 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm transition hover:shadow-md">
                        <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Siswa</dt>
                        <dd>
                            <x-filament::badge color="info" size="lg" class="px-4 py-1 text-lg font-black tracking-tight">
                                {{ number_format($stats['siswa']) }}
                            </x-filament::badge>
                        </dd>
                    </div>

                    <!-- Card GTK -->
                    <div class="flex flex-col items-center justify-center p-4 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm transition hover:shadow-md">
                        <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">GTK</dt>
                        <dd>
                            <x-filament::badge color="success" size="lg" class="px-4 py-1 text-lg font-black tracking-tight">
                                {{ number_format($stats['ptk']) }}
                            </x-filament::badge>
                        </dd>
                    </div>

                    <!-- Card Rombel -->
                    <div class="flex flex-col items-center justify-center p-4 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm transition hover:shadow-md">
                        <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Rombel</dt>
                        <dd>
                            <x-filament::badge color="danger" size="lg" class="px-4 py-1 text-lg font-black tracking-tight">
                                {{ number_format($stats['rombel']) }}
                            </x-filament::badge>
                        </dd>
                    </div>

                    <!-- Card Arsip -->
                    <div class="flex flex-col items-center justify-center p-4 rounded-xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-sm transition hover:shadow-md">
                        <dt class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Arsip File</dt>
                        <dd>
                            <x-filament::badge color="warning" size="lg" class="px-4 py-1 text-lg font-black tracking-tight">
                                {{ number_format($stats['file']) }}
                            </x-filament::badge>
                        </dd>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- KARTU TOOL MAINTENANCE -->
        <x-filament::section class="md:col-span-2">
            <x-slot name="heading">Aksi Pemeliharaan</x-slot>
            <div class="flex flex-wrap gap-4">
                {{ $this->backupAction }}
                
                {{ $this->resetDatabaseAction }}
            </div>
            
            <div class="mt-4 p-4 rounded-md bg-yellow-50 border-l-4 border-yellow-400">
                <p class="text-sm text-yellow-700">
                    <b>Catatan:</b> Gunakan fitur <i>Wipe Data</i> hanya jika ingin melakukan pembersihan total (misal: ganti tahun ajaran). Data yang dihapus <b>tidak bisa</b> dikembalikan.
                </p>
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>