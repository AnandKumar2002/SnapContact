<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl" level="1">Contacts</flux:heading>
                <flux:subheading>Manage and export your extracted contacts</flux:subheading>
            </div>

            <flux:button icon="plus" variant="primary" href="{{ route('contacts.create') }}" wire:navigate>
                Create Contact
            </flux:button>
        </div>

        <livewire:contact-table />
    </div>
</x-layouts::app>
