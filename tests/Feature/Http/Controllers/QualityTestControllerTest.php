<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\BatchAsBatch;
use App\Models\QualityTest;
use App\Models\UserAsUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\QualityTestController
 */
final class QualityTestControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $qualityTests = QualityTest::factory()->count(3)->create();

        $response = $this->get(route('quality-tests.index',['batch' => $qualityTests[0]->batch_id]));

        $response->assertOk();
        $response->assertViewIs('qualityTest.index');
        $response->assertViewHas('qualityTests');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('quality-tests.create'));

        $response->assertOk();
        $response->assertViewIs('qualityTest.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\QualityTestController::class,
            'store',
            \App\Http\Requests\QualityTestStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $batch_as_batch = BatchAsBatch::factory()->create();
        $user_as_user = UserAsUser::factory()->create();

        $response = $this->post(route('quality-tests.store'), [
            'batch_as_batch_id' => $batch_as_batch->id,
            'user_as_user_id' => $user_as_user->id,
        ]);

        $qualityTests = QualityTest::query()
            ->where('batch_as_batch_id', $batch_as_batch->id)
            ->where('user_as_user_id', $user_as_user->id)
            ->get();
        $this->assertCount(1, $qualityTests);
        $qualityTest = $qualityTests->first();

        $response->assertRedirect(route('qualityTests.index'));
        $response->assertSessionHas('qualityTest.id', $qualityTest->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $qualityTest = QualityTest::factory()->create();

        $response = $this->get(route('quality-tests.show', $qualityTest));

        $response->assertOk();
        $response->assertViewIs('qualityTest.show');
        $response->assertViewHas('qualityTest');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $qualityTest = QualityTest::factory()->create();

        $response = $this->get(route('quality-tests.edit', $qualityTest));

        $response->assertOk();
        $response->assertViewIs('qualityTest.edit');
        $response->assertViewHas('qualityTest');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\QualityTestController::class,
            'update',
            \App\Http\Requests\QualityTestUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $qualityTest = QualityTest::factory()->create();
        $batch_as_batch = BatchAsBatch::factory()->create();
        $user_as_user = UserAsUser::factory()->create();

        $response = $this->put(route('quality-tests.update', $qualityTest), [
            'batch_as_batch_id' => $batch_as_batch->id,
            'user_as_user_id' => $user_as_user->id,
        ]);

        $qualityTest->refresh();

        $response->assertRedirect(route('qualityTests.index'));
        $response->assertSessionHas('qualityTest.id', $qualityTest->id);

        $this->assertEquals($batch_as_batch->id, $qualityTest->batch_as_batch_id);
        $this->assertEquals($user_as_user->id, $qualityTest->user_as_user_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $qualityTest = QualityTest::factory()->create();

        $response = $this->delete(route('quality-tests.destroy', $qualityTest));

        $response->assertRedirect(route('qualityTests.index'));

        $this->assertModelMissing($qualityTest);
    }
}
