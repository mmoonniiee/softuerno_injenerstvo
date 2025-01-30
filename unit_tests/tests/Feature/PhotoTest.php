<?php

use App\Models\Album;
use App\Models\Photo;
use App\Models\Photographer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('can list album photos', function () {
    $photographer = Photographer::factory()->create();
    $album = Album::factory()->create(['photographer_id' => $photographer->id]);
    $photos = Photo::factory()->count(3)->create([
        'album_id' => $album->id
    ]);

    $response = $this->getJson("/api/photographers/{$photographer->id}/albums/{$album->id}/photos");

    $response->assertStatus(200)
        ->assertJsonCount(3)
        ->assertJsonStructure([
            '*' => ['id', 'name', 'album_id', 'created_at', 'updated_at']
        ]);
});

test('can create a photo in album', function () {
    $photographer = Photographer::factory()->create();
    $album = Album::factory()->create(['photographer_id' => $photographer->id]);
    
    $photoData = [
        'name' => 'Test Photo'
    ];

    $response = $this->postJson(
        "/api/photographers/{$photographer->id}/albums/{$album->id}/photos",
        $photoData
    );

    $response->assertStatus(201)
        ->assertJsonPath('0.name', 'Test Photo')
        ->assertJsonPath('0.album_id', $album->id);
    
    $this->assertDatabaseHas('photos', [
        'name' => 'Test Photo',
        'album_id' => $album->id
    ]);
});

test('can show a specific photo', function () {
    $photographer = Photographer::factory()->create();
    $album = Album::factory()->create(['photographer_id' => $photographer->id]);
    $photo = Photo::factory()->create([
        'album_id' => $album->id
    ]);

    $response = $this->getJson("/api/photographers/{$photographer->id}/albums/{$album->id}/photos/{$photo->id}");

    $response->assertStatus(200)
        ->assertJson([
            'id' => $photo->id,
            'name' => $photo->name,
            'album_id' => $album->id
        ]);
});

test('can update a photo', function () {
    $photographer = Photographer::factory()->create();
    $album = Album::factory()->create(['photographer_id' => $photographer->id]);
    $photo = Photo::factory()->create([
        'album_id' => $album->id
    ]);
    
    $updateData = [
        'name' => 'Updated Photo'
    ];

    $response = $this->putJson(
        "/api/photographers/{$photographer->id}/albums/{$album->id}/photos/{$photo->id}",
        $updateData
    );

    $response->assertStatus(200)
        ->assertJson($updateData);
    
    $this->assertDatabaseHas('photos', [
        'id' => $photo->id,
        'name' => 'Updated Photo'
    ]);
});

test('can delete a photo', function () {
    $photographer = Photographer::factory()->create();
    $album = Album::factory()->create(['photographer_id' => $photographer->id]);
    $photo = Photo::factory()->create([
        'album_id' => $album->id
    ]);

    $response = $this->deleteJson(
        "/api/photographers/{$photographer->id}/albums/{$album->id}/photos/{$photo->id}"
    );

    $response->assertStatus(204);
    $this->assertDatabaseMissing('photos', ['id' => $photo->id]);
});

test('cannot create photo without required fields', function () {
    $photographer = Photographer::factory()->create();
    $album = Album::factory()->create(['photographer_id' => $photographer->id]);

    $response = $this->postJson(
        "/api/photographers/{$photographer->id}/albums/{$album->id}/photos",
        []
    );

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});
