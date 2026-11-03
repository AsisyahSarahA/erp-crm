<?php

namespace Tests\Feature;

use App\Models\Prompt;
use Tests\TestCase;

class PromptRegistryTest extends TestCase
{
    public function test_prompt_crud_and_auto_versioning(): void
    {
        // 1. Create version 1
        $response = $this->postJson('/api/prompts', [
            'slug' => 'custom.assistant',
            'name' => 'Custom Assistant Prompt',
            'module' => 'pm',
            'system_prompt' => 'You are an agile coach.',
            'user_template' => 'Analyze sprint backlog for {{sprint_name}}.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.slug', 'custom.assistant')
            ->assertJsonPath('data.version', 1);

        // 2. Create version 2 of same slug
        $response2 = $this->postJson('/api/prompts', [
            'slug' => 'custom.assistant',
            'name' => 'Custom Assistant Prompt V2',
            'module' => 'pm',
            'system_prompt' => 'You are a senior agile coach.',
            'user_template' => 'Analyze sprint backlog for {{sprint_name}} with burndown.',
        ]);

        $response2->assertStatus(201)
            ->assertJsonPath('data.version', 2);

        $prompt = Prompt::where('slug', 'custom.assistant')->where('version', 2)->first();
        $this->assertEquals('Analyze sprint backlog for Sprint 14 with burndown.', $prompt->render(['sprint_name' => 'Sprint 14']));
    }
}

