<?php

namespace Tests\Feature;

use Guava\LaravelPopulator\Bundle;
use Guava\LaravelPopulator\Populator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Fixtures\TestPost;
use Tests\Fixtures\TestUser;
use Tests\TestCase;

class BundleTest extends TestCase
{
    use RefreshDatabase;

    public function test_handle_with_static_records()
    {
        $populator = Populator::make('manual');
        $bundle = Bundle::make(TestUser::class)
            ->records([
                'user-foo' => TestUser::factory()->raw([
                    'name' => 'Foo',
                    'email' => 'foo@example.com',
                ]),
            ]);
        $bundle->handle($populator);

        $this->assertTrue(TestUser::whereEmail('foo@example.com')->exists());
    }

    public function test_handle_with_filesystem_backed_records()
    {
        $populator = Populator::make('initial');
        (Bundle::make(TestUser::class))->handle($populator);
        (Bundle::make(TestPost::class))->handle($populator);

        $this->assertEquals(1, TestUser::whereEmail('foo@example.com')->count('posts'));

    }

    public function test_handle_with_filesystem_backed_records_throws_missing_directory_exception()
    {
        $this->expectExceptionMessageMatches('/^A directory for the bundle of/');
        $populator = Populator::make('initial_v2');
        $bundle = Bundle::make(TestUser::class);
        $bundle->handle($populator);
    }

    //    public function test_setup()
    //    {
    //
    //    }
    //
    //    public function test_make()
    //    {
    //
    //    }
}
