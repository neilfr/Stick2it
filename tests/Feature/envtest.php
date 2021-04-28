<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class envtest extends TestCase
{

    /** @test */
    public function test_example()
    {
        $dbenv1=getenv('CLEARDB_DATABASE_URL');
        $env2=getenv("FOO");
        dd($env2);
        $a = 1;
        $this->assertEquals(1, $a);

    }
}
