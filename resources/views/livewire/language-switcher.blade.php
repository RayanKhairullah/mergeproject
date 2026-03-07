<?php

namespace App\Livewire;

use Livewire\Volt\Component;

new class extends Component {
    public string $locale;
    public string $mode = 'dropdown'; // 'dropdown' or 'full'

    public function mount(string $mode = 'dropdown')
    {
        $this->locale = app()->getLocale();
        $this->mode = $mode;
    }

    public function switchLanguage($lang)
    {
        if (\Illuminate\Support\Facades\Auth::check()) {
            \Illuminate\Support\Facades\Auth::user()->update(['locale' => $lang]);
        }
        
        session()->put('locale', $lang);
        app()->setLocale($lang);
        $this->locale = $lang;
        
        $this->redirect(request()->header('Referer') ?? '/', navigate: true);
    }
}
?>

<div>
    @if($mode === 'full')
        <div class="flex flex-col gap-4 w-full">
            <!-- Theme Switcher -->
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500">{{ __('global.appearance') ?? 'Tampilan' }}</span>
                <div class="flex p-0.5 bg-zinc-200 dark:bg-zinc-700 rounded-lg shrink-0" x-data>
                    <button type="button" x-on:click="$flux.dark = false" class="p-1 px-2 rounded-md transition-all active:scale-95" x-bind:class="!$flux.dark ? 'bg-white shadow-sm text-teal-600' : 'text-zinc-500 opacity-50 hover:opacity-100'">
                        <flux:icon.sun class="size-3.5" />
                    </button>
                    <button type="button" x-on:click="$flux.dark = true" class="p-1 px-2 rounded-md transition-all active:scale-95" x-bind:class="$flux.dark ? 'bg-zinc-100 dark:bg-zinc-600 shadow-sm text-teal-600 dark:text-teal-400' : 'text-zinc-500 opacity-50 hover:opacity-100'">
                        <flux:icon.moon class="size-3.5" />
                    </button>
                </div>
            </div>

            <!-- Language Switcher -->
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500">{{ __('global.language') ?? 'Bahasa' }}</span>
                <div class="flex p-0.5 bg-zinc-200 dark:bg-zinc-700 rounded-lg shrink-0 overflow-hidden">
                    <button 
                        type="button" 
                        wire:click="switchLanguage('id')"
                        class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase transition-all active:scale-95 {{ $locale === 'id' ? 'bg-white dark:bg-zinc-600 text-teal-600 dark:text-teal-400 shadow-sm' : 'text-zinc-500 opacity-50 hover:opacity-100' }}"
                    >
                        ID
                    </button>
                    <button 
                        type="button" 
                        wire:click="switchLanguage('en')"
                        class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase transition-all active:scale-95 {{ $locale === 'en' ? 'bg-white dark:bg-zinc-600 text-teal-600 dark:text-teal-400 shadow-sm' : 'text-zinc-500 opacity-50 hover:opacity-100' }}"
                    >
                        EN
                    </button>
                </div>
            </div>
        </div>
    @else
        <flux:dropdown position="bottom" align="end">
            <button type="button" class="flex items-center justify-center transition-colors duration-200 shrink-0 outline-none {{ request()->routeIs('home') ? 'text-zinc-200 hover:text-white' : 'text-zinc-500 dark:text-white/70 hover:text-zinc-900 dark:hover:text-white' }}">
                <flux:icon.cog-6-tooth class="size-5 lg:size-6" />
            </button>
            <flux:menu class="min-w-48" x-data>
                <flux:menu.heading>{{ __('global.appearance') ?? 'Tampilan' }}</flux:menu.heading>
                <flux:menu.item x-on:click="$flux.dark = false" icon="sun" x-bind:class="!$flux.dark ? 'bg-zinc-100 dark:bg-zinc-700' : ''">Light Mode</flux:menu.item>
                <flux:menu.item x-on:click="$flux.dark = true" icon="moon" x-bind:class="$flux.dark ? 'bg-zinc-100 dark:bg-zinc-700' : ''">Dark Mode</flux:menu.item>
                
                <flux:menu.separator />
                
                <flux:menu.heading>{{ __('global.language') ?? 'Bahasa' }}</flux:menu.heading>
                <flux:menu.item wire:click="switchLanguage('id')" :icon="$locale === 'id' ? 'check' : ''">Indonesian</flux:menu.item>
                <flux:menu.item wire:click="switchLanguage('en')" :icon="$locale === 'en' ? 'check' : ''">English</flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    @endif
</div>
