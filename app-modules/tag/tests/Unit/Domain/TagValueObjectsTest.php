<?php

declare(strict_types=1);

namespace Modules\Tag\Tests\Unit\Domain;

use InvalidArgumentException;
use Modules\Tag\Domain\ValueObjects\TagName;
use Modules\Tag\Domain\ValueObjects\TagSlug;
use PHPUnit\Framework\TestCase;

class TagValueObjectsTest extends TestCase
{
    public function test_tag_name_cannot_be_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TagName('');
    }

    public function test_tag_name_trims_whitespace(): void
    {
        $name = new TagName('  Laravel  ');
        $this->assertEquals('Laravel', $name->value());
    }

    public function test_tag_slug_validates_format(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TagSlug('Invalid Slug!'); // Contains space and exclamation
    }

    public function test_tag_slug_accepts_valid_format(): void
    {
        $slug = new TagSlug('valid-slug-123');
        $this->assertEquals('valid-slug-123', $slug->value());
    }

    public function test_tag_slug_cannot_be_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TagSlug('');
    }
}
