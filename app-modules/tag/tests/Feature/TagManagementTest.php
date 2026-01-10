<?php

namespace Modules\Tag\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Models\TagModel;
use Tests\TestCase;

class TagManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    /**
     * @test
     */
    public function guest_cannot_access_tags(): void
    {
        $response = $this->get(route('tags.index'));

        $response->assertRedirect('login');
    }

    /**
     * @test
     */
    public function authenticated_user_can_list_tags(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('tags.index'));

        $response->assertOk();
    }

    /**
     * @test
     */
    public function authenticated_user_can_create_tag(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tags.store'), [
            'name' => 'New Tag',
            'slug' => 'new-tag',
        ]);

        $response->assertRedirect(route('tags.index'));
        $this->assertDatabaseHas('tags', [
            'name' => 'New Tag',
            'slug' => 'new-tag',
        ]);
    }

    /**
     * @test
     */
    public function authenticated_user_can_update_tag(): void
    {
        $user = User::factory()->create();
        $tag = TagModel::factory()->create([
            'name' => 'Old Tag',
            'slug' => 'old-tag',
        ]);

        $response = $this->actingAs($user)->put(route('tags.update', $tag->id), [
            'name' => 'Updated Tag',
            'slug' => 'updated-tag',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'Updated Tag',
            'slug' => 'updated-tag',
        ]);
    }

    /**
     * @test
     */
    public function authenticated_user_can_delete_tag(): void
    {
        $user = User::factory()->create();
        $tag = TagModel::factory()->create();

        $response = $this->actingAs($user)->delete(route('tags.destroy', $tag->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('tags', [
            'id' => $tag->id,
        ]);
    }
}
