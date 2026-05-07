<div class="max-w-4xl mx-auto space-y-8">

    {{-- Main Extraction Area --}}
    <flux:card class="space-y-6">
        <div>
            <flux:heading size="xl">AI Extraction</flux:heading>
            <flux:subheading>Upload handwritten images to digitize contacts instantly.</flux:subheading>
        </div>

        <div class="space-y-4">
            {{-- Native file input (flux:input type=file causes match errors in some Flux versions) --}}
            <div class="space-y-1">
                <flux:label>Handwritten List</flux:label>
                <input type="file" wire:model="image" accept="image/*"
                    class="block w-full text-sm text-zinc-700 dark:text-zinc-300
                           file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                           file:text-sm file:font-medium file:bg-zinc-100 file:text-zinc-700
                           hover:file:bg-zinc-200 dark:file:bg-zinc-700 dark:file:text-zinc-300
                           cursor-pointer" />
                <flux:error name="image" />
                <p class="text-xs text-zinc-500 dark:text-zinc-400">Supports JPG, PNG, WEBP (Max 10MB)</p>
            </div>

            {{-- Image Preview --}}
            @if ($image)
                <div class="space-y-2">
                    <flux:heading size="sm">Preview</flux:heading>
                    <img src="{{ $image->temporaryUrl() }}" alt="Image Preview"
                        class="max-h-64 rounded-lg border border-zinc-200 dark:border-zinc-700 object-contain shadow-sm" />
                </div>
            @endif
        </div>

        {{-- Extraction Button --}}
        <div class="flex items-center gap-3">
            <flux:button variant="primary" wire:click="extract" wire:loading.attr="disabled"
                wire:target="extract,image">
                <div wire:loading.remove wire:target="extract" class="flex items-center gap-2">
                    <flux:icon.sparkles class="size-4" />
                    <span>Run AI Extraction</span>
                </div>
                <div wire:loading wire:target="extract" class="flex items-center gap-2">
                    <flux:icon.arrow-path class="size-4 animate-spin" />
                    <span>AI is reading...</span>
                </div>
            </flux:button>

            {{-- Upload progress indicator --}}
            <div wire:loading wire:target="image" class="text-sm text-zinc-500 dark:text-zinc-400">
                Uploading...
            </div>
        </div>
    </flux:card>

    {{-- Review and Edit Section --}}
    @if (count($extractedContacts) > 0)
        <flux:card class="space-y-6">

            {{-- Header --}}
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="lg">Review Results</flux:heading>
                    <flux:subheading>Verify the data and edit any transcription mistakes inline.</flux:subheading>
                </div>
                <flux:badge color="indigo">{{ count($extractedContacts) }} Found</flux:badge>
            </div>

            {{-- Table --}}
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Phone Number</flux:table.column>
                    <flux:table.column align="end"></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($extractedContacts as $index => $contact)
                        <flux:table.row :key="'contact-'.$index">

                            {{-- Name Cell --}}
                            <flux:table.cell>
                                <input type="text" wire:model.blur="extractedContacts.{{ $index }}.name"
                                    placeholder="Name..."
                                    class="w-full bg-transparent text-sm text-zinc-900 dark:text-zinc-100
                                           placeholder-zinc-400 border-0 outline-none focus:ring-0 p-0" />
                                @error("extractedContacts.$index.name")
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </flux:table.cell>

                            {{-- Phone Cell --}}
                            <flux:table.cell>
                                <input type="text" wire:model.blur="extractedContacts.{{ $index }}.phone"
                                    placeholder="Phone..."
                                    class="w-full bg-transparent text-sm text-zinc-900 dark:text-zinc-100
                                           placeholder-zinc-400 border-0 outline-none focus:ring-0 p-0" />
                                @error("extractedContacts.$index.phone")
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </flux:table.cell>

                            {{-- Delete Row --}}
                            <flux:table.cell align="end">
                                <flux:button variant="ghost" size="sm" icon="trash"
                                    wire:click="remove({{ $index }})" wire:confirm="Remove this contact?" />
                            </flux:table.cell>

                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row justify-end gap-3 border-t border-zinc-200 dark:border-zinc-700 pt-6">
                <flux:button wire:click="$set('extractedContacts', [])">
                    Discard All
                </flux:button>

                <flux:button variant="primary" wire:click="save" wire:loading.attr="disabled" wire:target="save">
                    <div wire:loading.remove wire:target="save" class="flex items-center gap-2">
                        <flux:icon.check class="size-4" />
                        <span>Confirm & Save</span>
                    </div>
                    <div wire:loading wire:target="save" class="flex items-center gap-2">
                        <flux:icon.arrow-path class="size-4 animate-spin" />
                        <span>Saving...</span>
                    </div>
                </flux:button>
            </div>

        </flux:card>
    @endif

</div>
