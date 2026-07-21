<?php

use App\Enums\SsmType;
use App\Models\Supplier;
use App\Models\User;
use Livewire\Livewire;

it('renders the register page successfully', function () {
    $this->get('/register')
        ->assertOk()
        ->assertSeeLivewire('auth.register-form');
});

it('starts at step 1 and can transition to step 2', function () {
    Livewire::test('auth.register-form')
        ->assertSet('step', 1)
        ->call('setStep', 2)
        ->assertSet('step', 2);
});

it('rejects duplicate ssm numbers in step 2', function () {
    // Create an existing supplier
    $user = User::factory()->create();
    Supplier::factory()->create([
        'user_id' => $user->id,
        'ssm_number' => 'SSM-12345',
    ]);

    Livewire::test('auth.register-form')
        ->set('step', 2)
        ->set('ssm_no', 'SSM-12345')
        ->call('verifySSM')
        ->assertSet('verificationSuccess', false)
        ->assertSet('duplicateError', 'This SSM registration number has already been onboarded.');
});

it('accepts unique ssm numbers in step 2 and allows transition to step 3', function () {
    Livewire::test('auth.register-form')
        ->set('step', 2)
        ->set('ssm_no', 'SSM-UNIQUE-999')
        ->call('verifySSM')
        ->assertSet('verificationSuccess', true)
        ->assertSet('duplicateError', '')
        ->call('proceedToStep3')
        ->assertSet('step', 3);
});

it('requires valid user details in step 3 to proceed to step 4', function () {
    // Fail case
    Livewire::test('auth.register-form')
        ->set('step', 3)
        ->set('name', '')
        ->set('email', 'not-an-email')
        ->call('proceedToStep4')
        ->assertHasErrors(['name', 'email'])
        ->assertSet('step', 3);

    // Success case
    Livewire::test('auth.register-form')
        ->set('step', 3)
        ->set('name', 'John Doe')
        ->set('email', 'johndoe@example.com')
        ->call('proceedToStep4')
        ->assertHasNoErrors()
        ->assertSet('step', 4);
});

it('requires company details in step 4 to proceed to step 5', function () {
    // Fail case
    Livewire::test('auth.register-form')
        ->set('step', 4)
        ->set('company_name', '')
        ->set('ssm_type', 'invalid-type')
        ->call('proceedToStep5')
        ->assertHasErrors(['company_name', 'ssm_type'])
        ->assertSet('step', 4);

    // Success case
    Livewire::test('auth.register-form')
        ->set('step', 4)
        ->set('company_name', 'Acme Logistics Sdn Bhd')
        ->set('ssm_type', 'ROC: SENDIRIAN BERHAD')
        ->call('proceedToStep5')
        ->assertHasNoErrors()
        ->assertSet('step', 5);
});

it('requires password in step 5 to complete onboarding', function () {
    Livewire::test('auth.register-form')
        ->set('step', 5)
        ->set('ssm_no', 'SSM-NEW-123')
        ->set('name', 'John Doe')
        ->set('email', 'johndoe@example.com')
        ->set('company_name', 'Acme Logistics Sdn Bhd')
        ->set('ssm_type', 'ROC: SENDIRIAN BERHAD')
        ->set('password', 'short')
        ->set('password_confirmation', 'mismatch')
        ->call('register')
        ->assertHasErrors(['password'])
        ->assertSet('step', 5);
});

it('successfully registers supplier user and creates records in step 5', function () {
    Livewire::test('auth.register-form')
        ->set('step', 5)
        ->set('ssm_no', 'SSM-NEW-SUPPLIER')
        ->set('name', 'John Doe')
        ->set('email', 'johndoe@example.com')
        ->set('company_name', 'Acme Trading Sdn Bhd')
        ->set('ssm_type', 'ROC: SENDIRIAN BERHAD')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertRedirect(route('verification.notice'));

    // Check database entries
    $user = User::where('email', 'johndoe@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toEqual('John Doe');

    $supplier = Supplier::where('user_id', $user->id)->first();
    expect($supplier)->not->toBeNull();
    expect($supplier->company_name)->toEqual('Acme Trading Sdn Bhd');
    expect($supplier->ssm_number)->toEqual('SSM-NEW-SUPPLIER');
    expect($supplier->ssm_type)->toEqual(SsmType::ROC_SENDIRIAN_BERHAD);

    // Check user is authenticated
    expect(auth()->check())->toBeTrue();
    expect(auth()->id())->toEqual($user->id);
});
