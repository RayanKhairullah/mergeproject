<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public string $locale;

    public string $mode = 'dropdown'; // 'dropdown' or 'full'

    public function mount(string $mode = 'dropdown')
    {
        $this->locale = app()->getLocale();
        $this->mode = $mode;
    }

    public function switchLanguage(string $lang): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user) {
            $user->update(['locale' => $lang]);
        }

        session()->put('locale', $lang);
        app()->setLocale($lang);
        $this->locale = $lang;

        $this->redirect(request()->header('Referer') ?? '/', navigate: true);
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}
