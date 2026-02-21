<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- KARTU STATUS KONEKSI -->
        <x-filament::section icon="heroicon-o-signal" class="h-full">
            <x-slot name="heading">Sistem Health Check</x-slot>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800">
                    <span class="text-sm font-medium">Google Drive API</span>
                    <x-filament::badge :color="$driveStatus['success'] ? 'success' : 'danger'">
                        {{ $driveStatus['success'] ? 'Terhubung' : 'Terputus' }}
                    </x-filament::badge>
                </div>

                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800">
                    <span class="text-sm font-medium">Dapodik Web Service</span>
                    <x-filament::badge :color="$dapoStatus['success'] ? 'success' : 'danger'">
                        {{ $dapoStatus['success'] ? 'Online' : 'Offline' }}
                    </x-filament::badge>
                </div>
            </div>
        </x-filament::section>

        <!-- KARTU RINGKASAN DATA -->
        <x-filament::section icon="heroicon-o-circle-stack">
            <x-slot name="heading">Ringkasan Database</x-slot>
            <div class="grid grid-cols-2 gap-4">
                @foreach([
                    ['label' => 'Siswa', 'value' => $stats['siswa'], 'color' => 'info'],
                    ['label' => 'GTK', 'value' => $stats['ptk'], 'color' => 'success'],
                    ['label' => 'Rombel', 'value' => $stats['rombel'], 'color' => 'danger'],
                    ['label' => 'Arsip File', 'value' => $stats['file'], 'color' => 'warning'],
                ] as $stat)
                <div class="flex flex-col items-center justify-center p-3 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800">
                    <dt class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">{{ $stat['label'] }}</dt>
                    <dd>
                        <x-filament::badge :color="$stat['color']" size="lg" class="text-lg font-black">
                            {{ number_format($stat['value']) }}
                        </x-filament::badge>
                    </dd>
                </div>
                @endforeach
            </div>
        </x-filament::section>

        <!-- KARTU TOOL MAINTENANCE -->
        <x-filament::section class="md:col-span-2" icon="heroicon-o-wrench-screwdriver">
            <x-slot name="heading">Aksi Pemeliharaan</x-slot>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Backup -->
                <div class="p-4 border rounded-xl border-blue-100 bg-blue-50/30 dark:border-blue-900 dark:bg-blue-900/10">
                    <p class="text-xs font-bold text-blue-600 mb-2 uppercase">Cadangkan Data</p>
                    {{ $this->backupAction }}
                </div>

                <!-- Wipe DB -->
                <div class="p-4 border rounded-xl border-yellow-100 bg-yellow-50/30 dark:border-yellow-900 dark:bg-yellow-900/10">
                    <p class="text-xs font-bold text-yellow-600 mb-2 uppercase">Bersihkan Database</p>
                    {{ $this->wipeDatabaseAction }}
                </div>

                <!-- Nuclear -->
                <div class="p-4 border rounded-xl border-red-100 bg-red-50/30 dark:border-red-900 dark:bg-red-900/10">
                    <p class="text-xs font-bold text-red-600 mb-2 uppercase">Reset Total</p>
                    {{ $this->nuclearResetAction }}
                </div>
            </div>
            
            <div class="mt-6 p-4 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800">
                <h4 class="text-sm font-bold flex items-center gap-2 mb-1">
                    <x-filament::icon icon="heroicon-m-information-circle" class="h-4 w-4 text-primary-500" />
                    Catatan Penting
                </h4>
                <ul class="text-xs text-gray-600 dark:text-gray-400 list-disc ml-5 space-y-1">
                    <li><b>Backup Database</b> disarankan dilakukan sebelum melakukan aksi penghapusan apa pun.</li>
                    <li><b>Hapus Database Saja</b> berguna jika Anda ingin menyegarkan data dari Dapodik tanpa kehilangan file yang sudah diupload user di Google Drive.</li>
                    <li><b>Reset Total</b> akan menghapus seluruh data tanpa sisa. Gunakan hanya saat perpindahan tahun ajaran atau pembersihan sistem.</li>
                </ul>
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>