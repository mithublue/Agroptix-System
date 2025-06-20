<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Source;
use App\Models\UserAsOwner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SourceController
 */
final class SourceControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $sources = Source::factory()->count(3)->create();

        $response = $this->get(route('sources.index'));

        $response->assertOk();
        $response->assertViewIs('source.index');
        $response->assertViewHas('sources');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('sources.create'));

        $response->assertOk();
        $response->assertViewIs('source.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SourceController::class,
            'store',
            \App\Http\Requests\SourceStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $production_method = fake()->randomElement(/** enum_attributes **/);
        $status = fake()->word();
        $user_as_owner = UserAsOwner::factory()->create();

        $response = $this->post(route('sources.store'), [
            'production_method' => $production_method,
            'status' => $status,
            'user_as_owner_id' => $user_as_owner->id,
        ]);

        $sources = Source::query()
            ->where('production_method', $production_method)
            ->where('status', $status)
            ->where('user_as_owner_id', $user_as_owner->id)
            ->get();
        $this->assertCount(1, $sources);
        $source = $sources->first();

        $response->assertRedirect(route('sources.index'));
        $response->assertSessionHas('source.id', $source->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $source = Source::factory()->create();

        $response = $this->get(route('sources.show', $source));

        $response->assertOk();
        $response->assertViewIs('source.show');
        $response->assertViewHas('source');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $source = Source::factory()->create();

        $response = $this->get(route('sources.edit', $source));

        $response->assertOk();
        $response->assertViewIs('source.edit');
        $response->assertViewHas('source');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SourceController::class,
            'update',
            \App\Http\Requests\SourceUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $source = Source::factory()->create();
        $production_method = fake()->randomElement(/** enum_attributes **/);
        $status = fake()->word();
        $user_as_owner = UserAsOwner::factory()->create();

        $response = $this->put(route('sources.update', $source), [
            'production_method' => $production_method,
            'status' => $status,
            'user_as_owner_id' => $user_as_owner->id,
        ]);

        $source->refresh();

        $response->assertRedirect(route('sources.index'));
        $response->assertSessionHas('source.id', $source->id);

        $this->assertEquals($production_method, $source->production_method);
        $this->assertEquals($status, $source->status);
        $this->assertEquals($user_as_owner->id, $source->user_as_owner_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $source = Source::factory()->create();

        $response = $this->delete(route('sources.destroy', $source));

        $response->assertRedirect(route('sources.index'));

        $this->assertModelMissing($source);
    }
}
