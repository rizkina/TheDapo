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

            <!-- Peringatan jika PSQL tidak ditemukan -->
            @if(!$isPsqlReady)
            <div class="mb-6 p-3 rounded-lg bg-danger-50 border border-danger-200 flex items-center gap-3 text-danger-700">
                <x-filament::icon icon="heroicon-m-exclamation-triangle" class="h-5 w-5" />
                <p class="text-xs">
                    <b>Peringatan:</b> Perintah <code>psql</code> tidak terdeteksi. Fitur Backup & Restore dinonaktifkan. Periksa konfigurasi <b>.env</b> (PSQL_PATH).
                </p>
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- 1. Backup -->
                <div class="p-4 border rounded-xl border-blue-100 bg-blue-50/30 dark:border-blue-900/50 dark:bg-blue-900/10 flex flex-col justify-between">
                    <div>
                        <p class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-2">Cadangkan</p>
                        <p class="text-xs text-blue-700 dark:text-blue-300 mb-4">Buat salinan database terbaru dalam format .zip</p>
                    </div>
                    {{ $this->backupAction }}
                    <p class="mt-2 text-[9px] text-blue-500 italic">Terakhir: {{ $lastBackup }}</p>
                </div>

                <!-- 2. Restore -->
                <div class="p-4 border rounded-xl border-indigo-100 bg-indigo-50/30 dark:border-indigo-900/50 dark:bg-indigo-900/10 flex flex-col justify-between">
                    <div>
                        <p class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest mb-2">Pulihkan</p>
                        <p class="text-xs text-indigo-700 dark:text-indigo-300 mb-4">Unggah file .zip untuk mengembalikan data lama</p>
                    </div>
                    {{ $this->restoreAction }}
                    <p class="mt-2 text-[9px] text-indigo-500 italic text-center">Hanya file .zip hasil backup</p>
                </div>

                <!-- 3. Wipe DB -->
                <div class="p-4 border rounded-xl border-yellow-100 bg-yellow-50/30 dark:border-yellow-900/50 dark:bg-yellow-900/10 flex flex-col justify-between">
                    <div>
                        <p class="text-[10px] font-black text-yellow-600 dark:text-yellow-400 uppercase tracking-widest mb-2">Bersihkan</p>
                        <p class="text-xs text-yellow-700 dark:text-yellow-300 mb-4">Kosongkan semua tabel master tanpa hapus file fisik</p>
                    </div>
                    {{ $this->wipeDatabaseAction }}
                </div>

                <!-- 4. Nuclear -->
                <div class="p-4 border rounded-xl border-red-100 bg-red-50/30 dark:border-red-900/50 dark:bg-red-900/10 flex flex-col justify-between">
                    <div>
                        <p class="text-[10px] font-black text-red-600 dark:text-red-400 uppercase tracking-widest mb-2">Reset Total</p>
                        <p class="text-xs text-red-700 dark:text-red-300 mb-4">Hapus seluruh database dan file di Google Drive</p>
                    </div>
                    {{ $this->nuclearResetAction }}
                </div>
            </div>

            <!-- Catatan Penting -->
            <div class="mt-6 p-4 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-800">
                <h4 class="text-sm font-bold flex items-center gap-2 mb-2">
                    <x-filament::icon icon="heroicon-m-information-circle" class="h-4 w-4 text-primary-500" />
                    Panduan & Catatan Penting
                </h4>
                <ul class="text-[11px] text-gray-600 dark:text-gray-400 list-disc ml-5 space-y-1">
                    <li><b>Backup & Restore</b> memerlukan PostgreSQL Client Tools (psql) terinstal di server.</li>
                    <li><b>Restore Database</b> akan menimpa seluruh data saat ini. Pastikan Anda memiliki backup terbaru sebelum melakukan ini.</li>
                    <li><b>Hapus Database Saja</b> tidak akan menghapus foto di server lokal maupun dokumen di Google Drive.</li>
                    <li><b>Reset Total</b> bersifat permanen dan akan mengosongkan folder cloud yang terhubung.</li>
                </ul>
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>