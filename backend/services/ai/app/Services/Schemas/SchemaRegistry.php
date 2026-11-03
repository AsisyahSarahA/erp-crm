<?php

declare(strict_types=1);

namespace App\Services\Schemas;

class SchemaRegistry
{
    /**
     * Returns JSON schema array for a given schema identifier.
     */
    public static function getSchema(string $name): ?array
    {
        return match ($name) {
            'rab' => [
                'type' => 'object',
                'required' => ['summary', 'items', 'out_of_scope'],
                'properties' => [
                    'summary' => [
                        'type' => 'object',
                        'required' => ['total_mandays', 'duration_weeks', 'assumptions', 'risks'],
                    ],
                    'items' => [
                        'type' => 'array',
                    ],
                    'out_of_scope' => [
                        'type' => 'array',
                    ],
                ],
            ],
            'voicenote' => [
                'type' => 'object',
                'required' => ['executive_summary', 'core_needs', 'technical_scope', 'action_items'],
            ],
            'wa_reply' => [
                'type' => 'object',
                'required' => ['intent', 'suggested_reply', 'sentiment'],
            ],
            'lead_summary' => [
                'type' => 'object',
                'required' => ['company_profile', 'qualification_score'],
            ],
            'mou_clause' => [
                'type' => 'object',
                'required' => ['clause_title', 'clause_text', 'risk_level'],
            ],
            'content' => [
                'type' => 'object',
                'required' => ['format', 'headlines', 'body', 'call_to_action'],
            ],
            default => null,
        };
    }

    /**
     * Validates a data array against a schema structure.
     *
     * @return array{is_valid: bool, errors: string[]}
     */
    public static function validate(array $data, array $schema): array
    {
        $errors = [];

        if (isset($schema['required']) && is_array($schema['required'])) {
            foreach ($schema['required'] as $field) {
                if (!array_key_exists($field, $data)) {
                    $errors[] = "Missing required field: '{$field}'";
                }
            }
        }

        if (isset($schema['properties']) && is_array($schema['properties'])) {
            foreach ($schema['properties'] as $propName => $propDef) {
                if (isset($data[$propName]) && is_array($data[$propName])) {
                    if (isset($propDef['required']) && is_array($propDef['required'])) {
                        foreach ($propDef['required'] as $nestedReq) {
                            if (!array_key_exists($nestedReq, $data[$propName])) {
                                $errors[] = "Missing nested required field: '{$propName}.{$nestedReq}'";
                            }
                        }
                    }
                }
            }
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}

