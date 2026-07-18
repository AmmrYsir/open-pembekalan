<?php

use Livewire\Livewire;

it('renders the portal page successfully', function () {
    $this->get('/portal')
        ->assertOk()
        ->assertSeeLivewire('portal.portal-advertisements');
});

it('lists all advertisements by default', function () {
    Livewire::test('portal.portal-advertisements')
        ->assertSet('search', '')
        ->assertSet('selectedCategory', 'all')
        ->assertSet('selectedStatus', 'all')
        ->assertCount('advertisements', 5);
});

it('filters advertisements by search query', function () {
    Livewire::test('portal.portal-advertisements')
        ->set('search', 'Laptops')
        ->assertCount('advertisements', 1)
        ->assertSee('KPM/2026/QT/089');
});

it('filters advertisements by category selection', function () {
    Livewire::test('portal.portal-advertisements')
        ->set('selectedCategory', 'Services')
        ->assertCount('advertisements', 2)
        ->assertSee('Cloud-Based Procurement')
        ->assertSee('Main Server Infrastructure');
});

it('filters advertisements by status selection', function () {
    Livewire::test('portal.portal-advertisements')
        ->set('selectedStatus', 'Closing Soon')
        ->assertCount('advertisements', 1)
        ->assertSee('MAMPU/2026/QT/012');
});
