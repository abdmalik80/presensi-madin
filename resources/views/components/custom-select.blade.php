@props(['name', 'id' => null, 'options', 'selected' => '', 'placeholder' => 'Pilih Opsi', 'onchange' => '', 'width' => 'w-full md:w-56'])

@php
    $initialText = $placeholder;
    if ((string)$selected !== '' && isset($options[$selected])) {
        $initialText = trim(strip_tags($options[$selected]));
    }
@endphp

<div class="relative {{ $width }}" :style="open ? 'z-index: 9999;' : 'z-index: 10;'" x-data="{ 
    open: false, 
    value: '{{ $selected }}',
    get label() {
        if (!this.value) return '{{ $placeholder }}';
        let el = this.$refs.list.querySelector(`[data-val='${this.value}']`);
        return el ? el.innerText.trim() : '{{ $placeholder }}';
    }
}" x-init="@if($onchange) $watch('value', val => { {!! str_replace('this.value', 'val', $onchange) !!} }) @endif">
    <input type="hidden" name="{{ $name }}" id="{{ $id ?? $name }}" x-model="value">
    <button @click="open = !open" @click.away="open = false" type="button" class="w-full min-h-[38px] border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-hijau-tua focus:border-hijau-tua focus:outline-none bg-white flex justify-between items-center text-left transition-shadow">
        <span x-text="label" class="truncate whitespace-nowrap text-gray-700">{{ $initialText }}</span>
        <i class="fas fa-chevron-down text-gray-400 text-[10px] ml-2 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
    </button>
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 "
         x-transition:enter-end="opacity-100 "
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 "
         x-transition:leave-end="opacity-0 "
         style="display: none; z-index: 9999;" 
         class="absolute min-w-full w-max mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto" 
         x-ref="list">
        <div class="p-1 flex flex-col gap-0.5">
            <button type="button" @click="value = ''; open = false" data-val="" class="text-left px-3 py-2 text-sm rounded-md hover:bg-gray-100 transition-colors truncate" :class="value == '' ? 'bg-hijau-tua/10 text-hijau-tua font-medium' : 'text-gray-700'">
                {{ $placeholder }}
            </button>
            @foreach($options as $val => $text)
                <button type="button" @click="value = '{{ $val }}'; open = false" data-val="{{ $val }}" class="text-left px-3 py-2 text-sm rounded-md hover:bg-gray-100 transition-colors truncate" :class="value == '{{ $val }}' ? 'bg-hijau-tua/10 text-hijau-tua font-medium' : 'text-gray-700'">
                    {!! $text !!}
                </button>
            @endforeach
        </div>
    </div>
</div>
