<div class="flex items-center justify-center" x-on:click.stop>
    <input
        type="checkbox"
        value="{{ $getRecord()->getKey() }}"
        wire:model.live="selectedKelasIds"
        class="fi-checkbox-input rounded border-gray-300 text-primary-600 shadow-sm outline-none focus:ring-1 focus:ring-primary-600 dark:border-white/10 dark:bg-white/5 dark:checked:bg-primary-500"
    />
</div>
