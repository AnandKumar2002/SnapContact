<?php

use App\Ai\Agents\ContactExtraction;
use App\Models\Contact;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public $image;

    public array $extractedContacts = [];

    /**
     * Extract contacts using AI
     */
    public function extract(): void
    {
        $this->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        $this->extractedContacts = []; // reset previous results

        try {
            $result = (new ContactExtraction(
                files: [$this->image],
            ))->prompt('Extract all handwritten names and phone numbers from this image.');

            $contacts = data_get($result, 'contacts', []);

            $this->extractedContacts = collect($contacts)
                ->map(fn ($c) => [
                    'name' => trim($c['name'] ?? ''),
                    'phone' => trim($c['phone'] ?? ''),
                ])
                ->filter(fn ($c) => filled($c['name']) || filled($c['phone']))
                ->values()
                ->toArray();

            if (empty($this->extractedContacts)) {
                Flux::toast(
                    heading: 'No Contacts Found',
                    text: 'The AI could not detect any valid contacts in this image.',
                    variant: 'warning',
                );

                return;
            }

            Flux::toast(
                heading: 'Extraction Complete',
                text: count($this->extractedContacts).' contact(s) found. Review below.',
                variant: 'success',
            );

        } catch (Throwable $e) {
            report($e);

            // User-friendly message vs raw curl/SSL errors
            $message = app()->isLocal()
                ? $e->getMessage()
                : 'AI extraction failed. Please try again or contact support.';

            Flux::toast(
                heading: 'AI Error',
                text: $message,
                variant: 'danger',
            );
        }
    }

    /**
     * Remove extracted row
     */
    public function remove(int $index): void
    {
        unset($this->extractedContacts[$index]);

        $this->extractedContacts = array_values(
            $this->extractedContacts
        );
    }

    /**
     * Save contacts
     */
    public function save()
    {
        $this->validate([
            'extractedContacts.*.name' => 'nullable|string|max:255',
            'extractedContacts.*.phone' => 'nullable|string|max:255',
        ]);

        foreach ($this->extractedContacts as $contact) {

            /**
             * Skip empty rows
             */
            if (
                blank($contact['name'] ?? null) &&
                blank($contact['phone'] ?? null)
            ) {
                continue;
            }

            Contact::create([
                'name' => $contact['name'] ?? '',
                'phone' => $contact['phone'] ?? '',
            ]);
        }

        Flux::toast(
            heading: 'Saved',
            text: 'Contacts saved successfully.',
            variant: 'success',
        );

        $this->reset([
            'image',
            'extractedContacts',
        ]);

        return redirect()->route('contacts.index');
    }
};
