<?php

namespace CraigPaul\Moneris\TestSupport\Verification;

/**
 * @see https://developer.moneris.com/More/Testing/E-Fraud%20Simulator
 */
class MasterCardSource extends CardSourceAbstract
{
    protected function cards(): array
    {
        return [
            ['5204740000003503', 'Approved', 'X', null],
            ['5204740000003602', 'Approved', 'Y', null],
            ['5204740000004105', 'Approved', 'Z', null],
            ['5204740000004204', 'Approved', 'W', null],
            ['5204740000004311', 'Approved', 'A', null],
            ['5204740000004337', 'Approved', 'N', null],
            ['5204740000004352', 'Approved', 'U', null],
            ['5204740000004378', 'Approved', 'R', null],
            ['5186000600001668', 'Approved', 'S', null],
            ['5204740000000517', 'Approved', 'X', 'M'],
            ['5204740000000525', 'Approved', 'Y', 'M'],
            ['5204740000000608', 'Declined', 'N', 'N'],
            ['5204740000000616', 'Declined', 'U', 'N'],
        ];
    }
}
