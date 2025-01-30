<?php

use App\Models\Photographer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can list photographers', function () {
    $photographers = Photographer::factory()->count(3)->create();

    $response = $this->getJson('/api/photographers');

    $response->assertStatus(200)
        ->assertJsonCount(3)
        ->assertJsonStructure([
            '*' => ['id', 'name', 'instagram', 'created_at', 'updated_at']
        ]);
});

test('can create a photographer', function () {
    $photographerData = [
        'name' => 'John Doe',
        'instagram' => '@johndoe'
    ];

    $response = $this->postJson('/api/photographers', $photographerData);

    $response->assertStatus(201)
        ->assertJsonPath('0.name', 'John Doe')
        ->assertJsonPath('0.instagram', '@johndoe');
    
    $this->assertDatabaseHas('photographers', $photographerData);
});

test('can show a photographer', function () {
    $photographer = Photographer::factory()->create();

    $response = $this->getJson("/api/photographers/{$photographer->id}");

    $response->assertStatus(200)
        ->assertJson([
            'id' => $photographer->id,
            'name' => $photographer->name,
            'instagram' => $photographer->instagram
        ]);
});

test('can update a photographer', function () {
    $photographer = Photographer::factory()->create();
    $updateData = [
        'name' => 'Updated Name',
        'instagram' => '@updated'
    ];

    $response = $this->putJson("/api/photographers/{$photographer->id}", $updateData);

    $response->assertStatus(200)
        ->assertJson($updateData);
    
    $this->assertDatabaseHas('photographers', $updateData);
});

test('can delete a photographer', function () {
    $photographer = Photographer::factory()->create();

    $response = $this->deleteJson("/api/photographers/{$photographer->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('photographers', ['id' => $photographer->id]);
});

test('cannot create a photographer without required fields', function () {
    $response = $this->postJson('/api/photographers', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('cannot update photographer with invalid data', function () {
    $photographer = Photographer::factory()->create();

    $response = $this->putJson("/api/photographers/{$photographer->id}", [
        'name' => ''
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});
