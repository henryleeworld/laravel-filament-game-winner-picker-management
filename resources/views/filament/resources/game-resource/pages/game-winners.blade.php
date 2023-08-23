<x-filament::page>
    <form wire:submit.prevent="submit">
        {{ $this->form }}

        <x-filament-support::button type="submit" class="mt-4">
            {{ __('Save') }}
        </x-filament-support::button>
    </form>
</x-filament::page>
