<?php

use App\Models\Album;
use App\Models\Photographer;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can list photographer albums', function () {
    $photographer = Photographer::factory()->create();
    $albums = Album::factory()->count(3)->create([
        'photographer_id' => $photographer->id
    ]);

    $response = $this->getJson("/api/photographers/{$photographer->id}/albums");

    $response->assertStatus(200)
        ->assertJsonCount(3)
        ->assertJsonStructure([
            '*' => ['id', 'name', 'photographer_id', 'created_at', 'updated_at']
        ]);
});

test('can create an album for photographer', function () {
    $photographer = Photographer::factory()->create();
    $albumData = [
        'name' => 'Test Album'
    ];

    $response = $this->postJson("/api/photographers/{$photographer->id}/albums", $albumData);

    $response->assertStatus(201)
        ->assertJsonPath('0.name', 'Test Album')
        ->assertJsonPath('0.photographer_id', $photographer->id);
    
    $this->assertDatabaseHas('albums', [
        'name' => 'Test Album',
        'photographer_id' => $photographer->id
    ]);
});

test('can show a specific album', function () {
    $photographer = Photographer::factory()->create();
    $album = Album::factory()->create([
        'photographer_id' => $photographer->id
    ]);

    $response = $this->getJson("/api/photographers/{$photographer->id}/albums/{$album->id}");

    $response->assertStatus(200)
        ->assertJson([
            'id' => $album->id,
            'name' => $album->name,
            'photographer_id' => $photographer->id
        ]);
});

test('can update an album', function () {
    $photographer = Photographer::factory()->create();
    $album = Album::factory()->create([
        'photographer_id' => $photographer->id
    ]);
    $updateData = [
        'name' => 'Updated Album'
    ];

    $response = $this->putJson("/api/photographers/{$photographer->id}/albums/{$album->id}", $updateData);

    $response->assertStatus(200)
        ->assertJson($updateData);
    
    $this->assertDatabaseHas('albums', [
        'id' => $album->id,
        'name' => 'Updated Album'
    ]);
});

test('can delete an album', function () {
    $photographer = Photographer::factory()->create();
    $album = Album::factory()->create([
        'photographer_id' => $photographer->id
    ]);

    $response = $this->deleteJson("/api/photographers/{$photographer->id}/albums/{$album->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('albums', ['id' => $album->id]);
});

test('cannot create album without required fields', function () {
    $photographer = Photographer::factory()->create();

    $response = $this->postJson("/api/photographers/{$photographer->id}/albums", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});
