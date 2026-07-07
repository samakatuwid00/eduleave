<?php

use App\Services\LeaveCardNormalizer;
use Tests\TestCase;

uses(TestCase::class);

test('normalizer parses supported reporting periods', function (string $value, string $start, string $end) {
    $result = app(LeaveCardNormalizer::class)->period($value);

    expect($result)->toMatchArray([
        'start' => $start,
        'end' => $end,
        'state' => LeaveCardNormalizer::PARSED,
    ]);
})->with([
    ['July 2026', '2026-07-01', '2026-07-31'],
    ['Jul 2026', '2026-07-01', '2026-07-31'],
    ['2026-07', '2026-07-01', '2026-07-31'],
    ['2026-07-15', '2026-07-15', '2026-07-15'],
]);

test('normalizer does not guess an ambiguous reporting period', function () {
    expect(app(LeaveCardNormalizer::class)->period('July to August 2026'))
        ->toMatchArray([
            'start' => null,
            'end' => null,
            'state' => LeaveCardNormalizer::UNPARSEABLE,
        ]);
});

test('normalizer parses known quantity formats without converting unknown text to zero', function () {
    $normalizer = app(LeaveCardNormalizer::class);

    expect($normalizer->quantity('1 day'))->toMatchArray(['value' => 1.0, 'state' => 'parsed'])
        ->and($normalizer->quantity('None'))->toMatchArray(['value' => 0.0, 'state' => 'parsed'])
        ->and($normalizer->quantity('N/A'))->toMatchArray(['value' => null, 'state' => 'not_applicable'])
        ->and($normalizer->quantity('about two'))->toMatchArray([
            'value' => null,
            'state' => 'unparseable',
        ]);
});
