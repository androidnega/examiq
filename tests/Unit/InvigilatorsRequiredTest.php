<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class InvigilatorsRequiredTest extends TestCase
{
    public function test_invigilators_required_rounds_up_per_forty_students(): void
    {
        require_once __DIR__.'/../../app/helpers.php';

        $this->assertSame(0, invigilators_required(0));
        $this->assertSame(1, invigilators_required(1));
        $this->assertSame(1, invigilators_required(40));
        $this->assertSame(2, invigilators_required(41));
        $this->assertSame(3, invigilators_required(85));
    }
}
