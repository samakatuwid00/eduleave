<?php

use App\Models\PersonnelType;
use Tests\TestCase;

uses(TestCase::class);

test('card mutation routes include a constrained personnel type', function () {
    expect(route('admin.card-info.update', [
        'cardType' => PersonnelType::CODE_TEACHING,
        'id' => 10,
    ], false))->toBe('/admin/card_info/teaching/10')
        ->and(route('admin.card-info.destroy', [
            'cardType' => PersonnelType::CODE_NON_TEACHING,
            'id' => 11,
        ], false))->toBe('/admin/card_info/non_teaching/11');
});

test('card mutation routes reject an unsupported personnel type', function () {
    $this->put('/admin/card_info/contractor/10')->assertNotFound();
    $this->delete('/admin/card_info/contractor/10')->assertNotFound();
});
