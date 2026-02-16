<x-filament-widgets::widget>
    <div class="space-y-4">
        @php $items = $this->getAnnouncements(); @endphp

        @foreach($items as $item)
            @php $config = $this->getTypeConfig($item->tipe); @endphp
            
            <x-filament::section 
                :icon="$config['icon']" 
                :icon-color="$config['color']"
                collapsible
            >
                <x-slot name="heading">
                    <div class="flex flex-col md:flex-row md:items-center justify-between w-full gap-2">
                        
                        <!-- Sisi Kiri: Judul dan Badge -->
                        <div class="flex items-center gap-3">
                            <span class="text-lg font-bold tracking-tight text-gray-950 dark:text-white">
                                {{ $item->judul }}
                            </span>
                            <x-filament::badge :color="$config['color']" size="sm">
                                {{ $this->getLabel($item->tipe) }}
                            </x-filament::badge>
                        </div>

                        <!-- Sisi Kanan: Tanggal (Rata Kanan & Warna Abu-abu) -->
                        {{-- <div class="flex items-center">
                            <time class="text-xs text-gray-100 dark:text-gray-400 italic font-sm whitespace-nowrap opacity-70">
                                {{ $item->created_at->format('d-m-Y H:i') }}
                            </time>
                        </div> --}}
                    </div>
                </x-slot>
                
                {{-- <x-slot name="headerEnd">
                    <div class="flex items-center justify-end whitespace-nowrap px-2">
                        <time class="text-xs text-gray-500 dark:text-gray-400 italic">
                            {{ $item->created_at->diffForHumans() }}
                        </time>
                    </div>
                </x-slot> --}}

                {{-- Tampilan konten Rich Editor --}}
                <div class="prose dark:prose-invert max-w-none text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                    {!! $item->konten !!}
                </div>
            </x-filament::section>
        @endforeach
    </div>

    {{-- Sembunyikan container widget jika tidak ada pengumuman --}}
    @if($items->isEmpty())
        <style>
            .fi-wi-announcement-widget { display: none !important; }
        </style>
    @endif
</x-filament-widgets::widget>