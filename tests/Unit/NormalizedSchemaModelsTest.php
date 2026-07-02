<?php

use App\Models\EmployeeProfile;
use App\Models\NonTeachingLeaveCard;
use App\Models\PersonnelType;
use App\Models\TeachingLeaveCard;
use App\Models\User;
use Tests\TestCase;

uses(TestCase::class);

test('user stores account fields and relates to one employee profile', function () {
    $user = new User;
    $relation = $user->employeeProfile();

    expect($user->getFillable())->toBe([
        'name',
        'email',
        'phone',
        'password',
    ])->and($relation->getRelated())->toBeInstanceOf(EmployeeProfile::class)
        ->and($relation->getForeignKeyName())->toBe('user_id')
        ->and($relation->getLocalKeyName())->toBe('id');
});

test('employee profile resolves the teaching leave card table', function () {
    $profile = persistedProfileFor(PersonnelType::CODE_TEACHING);

    expect($profile->leaveCardModelClass())->toBe(TeachingLeaveCard::class)
        ->and($profile->leaveCardQuery()->getModel())->toBeInstanceOf(TeachingLeaveCard::class);
});

test('employee profile resolves the non teaching leave card table', function () {
    $profile = persistedProfileFor(PersonnelType::CODE_NON_TEACHING);

    expect($profile->leaveCardModelClass())->toBe(NonTeachingLeaveCard::class)
        ->and($profile->leaveCardQuery()->getModel())->toBeInstanceOf(NonTeachingLeaveCard::class);
});

test('leave card relationships use the employee profile user id', function () {
    $profile = new EmployeeProfile;
    $teachingCards = $profile->teachingLeaveCards();
    $nonTeachingCards = $profile->nonTeachingLeaveCards();

    expect($teachingCards->getRelated())->toBeInstanceOf(TeachingLeaveCard::class)
        ->and($teachingCards->getForeignKeyName())->toBe('employee_profile_id')
        ->and($teachingCards->getLocalKeyName())->toBe('user_id')
        ->and($nonTeachingCards->getRelated())->toBeInstanceOf(NonTeachingLeaveCard::class)
        ->and($nonTeachingCards->getForeignKeyName())->toBe('employee_profile_id')
        ->and($nonTeachingCards->getLocalKeyName())->toBe('user_id');
});

test('employee profile rejects an unsupported personnel type', function () {
    persistedProfileFor('contractor')->leaveCardModelClass();
})->throws(DomainException::class, 'Unsupported personnel type');

function persistedProfileFor(string $personnelTypeCode): EmployeeProfile
{
    $profile = new EmployeeProfile;
    $profile->forceFill(['user_id' => 42]);
    $profile->exists = true;
    $profile->setRelation('personnelType', new PersonnelType([
        'code' => $personnelTypeCode,
    ]));

    return $profile;
}
