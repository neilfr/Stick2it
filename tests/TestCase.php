<?php

namespace Tests;

use Illuminate\Support\Arr;
use PHPUnit\Framework\Assert;
use Illuminate\Contracts\View\View;
use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();


        TestResponse::macro('ensureResponseHasInertia', function () {
            if (!isset($this->original) || !$this->original instanceof View || !isset($this->original->getData()['page'])) {
                return Assert::fail('The response is not an Inertia view.');
            }
        });

        TestResponse::macro('props', function ($key = null) {
            $this->ensureResponseHasInertia();

            $props = json_decode(json_encode($this->original->getData()['page']['props']), JSON_OBJECT_AS_ARRAY);

            if ($key) {
                return Arr::get($props, $key);
            }

            return $props;
        });

        TestResponse::macro('assertPage', function ($page) {
            $this->ensureResponseHasInertia();

            Assert::assertEquals($this->original->getData()['page']['component'], $page);

            return $this;
        });

        TestResponse::macro('assertHasProp', function ($key) {
            $this->ensureResponseHasInertia();

            Assert::assertTrue(Arr::has($this->props(), $key));

            return $this;
        });

        TestResponse::macro('assertPropValue', function ($key, $value) {
            $this->ensureResponseHasInertia();
            $this->assertHasProp($key);

            if (is_callable($value)) {
                $value($this->props($key));
            } else {
                Assert::assertEquals($this->props($key), $value);
            }

            return $this;
        });

        TestResponse::macro('assertPropCount', function ($key, $count) {
            $this->ensureResponseHasInertia();

            $this->assertHasProp($key);

            Assert::assertCount($count, $this->props($key));

            return $this;
        });
    }
}
