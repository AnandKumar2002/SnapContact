<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\ArrayType;
use Illuminate\JsonSchema\Types\ObjectType;
use Illuminate\JsonSchema\Types\StringType;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class ContactExtraction implements Agent, Conversational, HasStructuredOutput
{
    use Promptable;

    public function __construct(public array $files = []) {}

    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
You are an OCR and contact extraction specialist.

Extract ALL names and phone numbers from the provided image.

Rules:
- Return ONLY valid contacts with name and/or phone
- Clean phone numbers: keep digits and leading '+' only, min 7 digits
- If name is unreadable: use empty string ""
- If phone is unreadable: use empty string ""  
- No duplicates, no invented data
- Ignore emails, addresses, headers, notes

Return ONLY JSON — no markdown, no explanation.
PROMPT;
    }

    public function messages(): iterable
    {
        return [];
    }

    public function tools(): iterable
    {
        return [];
    }

    public function files(): array
    {
        return $this->files;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'contacts' => new ArrayType(
                new ObjectType(
                    [
                        'name' => new StringType,
                        'phone' => new StringType,
                    ],
                    ['name', 'phone']
                )
            ),
        ];
    }
}
