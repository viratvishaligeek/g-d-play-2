<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_homepage_fails_closed_when_shared_business_tables_are_not_configured(): void
    {
        $this->get('/')->assertStatus(503);
    }
}
