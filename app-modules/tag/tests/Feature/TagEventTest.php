<?php

declare(strict_types=1);

namespace Modules\Tag\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Modules\Tag\Application\CommandHandlers\CreateTagHandler;
use Modules\Tag\Application\CommandHandlers\UpdateTagHandler;
use Modules\Tag\Application\Commands\CreateTagCommand;
use Modules\Tag\Application\Commands\UpdateTagCommand;
use Modules\Tag\Domain\Events\TagCreated;
use Modules\Tag\Domain\Events\TagUpdated;
use Modules\Tag\Domain\Exceptions\SlugAlreadyExistsException;
use Modules\Tag\Infrastructure\Persistence\Eloquent\Models\TagModel;
use Tests\TestCase;

class TagEventTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_create_emits_tag_created_with_id(): void
    {
        Event::fake([TagCreated::class]);

        $handler = app(CreateTagHandler::class);
        $command = new CreateTagCommand('Test Tag', 'test-tag');

        $handler->handle($command);

        Event::assertDispatched(TagCreated::class, function (TagCreated $event) {
            return $event->id->value() > 0 &&
                   $event->name->value() === 'Test Tag' &&
                   $event->slug->value() === 'test-tag';
        });
    }

    /**
     * @test
     */
    public function test_update_emits_only_one_tag_updated(): void
    {
        $tagModel = TagModel::factory()->create(['name' => 'Old Name', 'slug' => 'old-slug']);

        Event::fake([TagUpdated::class]);

        $handler = app(UpdateTagHandler::class);
        $command = new UpdateTagCommand($tagModel->id, 'New Name', 'new-slug');

        $handler->handle($command);

        Event::assertDispatchedTimes(TagUpdated::class, 1);
    }

    /**
     * @test
     */
    public function test_slug_conflict_throws_slug_already_exists_exception(): void
    {
        TagModel::factory()->create(['slug' => 'existing-slug']);

        $handler = app(CreateTagHandler::class);
        $command = new CreateTagCommand('New Tag', 'existing-slug');

        $this->expectException(SlugAlreadyExistsException::class);

        $handler->handle($command);
    }
}
