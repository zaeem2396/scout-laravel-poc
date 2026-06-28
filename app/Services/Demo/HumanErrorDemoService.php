<?php

namespace App\Services\Demo;

use App\Exceptions\HumanErrorDemoException;

class HumanErrorDemoService
{
    /**
     * Simulate a user-caused business error (invalid input, bad state, etc.).
     */
    public function execute(): never
    {
        throw new HumanErrorDemoException(
            'Checkout failed: coupon code "SAVE50" is expired or was already used.',
        );
    }
}
