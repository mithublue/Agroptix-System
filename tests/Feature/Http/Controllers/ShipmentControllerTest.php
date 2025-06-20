<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\BatchAsBatch;
use App\Models\Shipment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ShipmentController
 */
final class ShipmentControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $shipments = Shipment::factory()->count(3)->create();

        $response = $this->get(route('shipments.index'));

        $response->assertOk();
        $response->assertViewIs('shipment.index');
        $response->assertViewHas('shipments');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('shipments.create'));

        $response->assertOk();
        $response->assertViewIs('shipment.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ShipmentController::class,
            'store',
            \App\Http\Requests\ShipmentStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $batch_as_batch = BatchAsBatch::factory()->create();

        $response = $this->post(route('shipments.store'), [
            'batch_as_batch_id' => $batch_as_batch->id,
        ]);

        $shipments = Shipment::query()
            ->where('batch_as_batch_id', $batch_as_batch->id)
            ->get();
        $this->assertCount(1, $shipments);
        $shipment = $shipments->first();

        $response->assertRedirect(route('shipments.index'));
        $response->assertSessionHas('shipment.id', $shipment->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $shipment = Shipment::factory()->create();

        $response = $this->get(route('shipments.show', $shipment));

        $response->assertOk();
        $response->assertViewIs('shipment.show');
        $response->assertViewHas('shipment');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $shipment = Shipment::factory()->create();

        $response = $this->get(route('shipments.edit', $shipment));

        $response->assertOk();
        $response->assertViewIs('shipment.edit');
        $response->assertViewHas('shipment');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ShipmentController::class,
            'update',
            \App\Http\Requests\ShipmentUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $shipment = Shipment::factory()->create();
        $batch_as_batch = BatchAsBatch::factory()->create();

        $response = $this->put(route('shipments.update', $shipment), [
            'batch_as_batch_id' => $batch_as_batch->id,
        ]);

        $shipment->refresh();

        $response->assertRedirect(route('shipments.index'));
        $response->assertSessionHas('shipment.id', $shipment->id);

        $this->assertEquals($batch_as_batch->id, $shipment->batch_as_batch_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $shipment = Shipment::factory()->create();

        $response = $this->delete(route('shipments.destroy', $shipment));

        $response->assertRedirect(route('shipments.index'));

        $this->assertModelMissing($shipment);
    }
}
