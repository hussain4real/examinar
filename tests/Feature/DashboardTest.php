<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated admin is redirected to admin panel', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect('/admin');
});

test('authenticated student can visit the dashboard', function () {
    $user = User::factory()->create(['role' => 'student']);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Dashboard')
        ->has('stats')
        ->missing('recentAttempts')
        ->loadDeferredProps(fn ($reload) => $reload
            ->has('recentAttempts')
        )
    );
});
