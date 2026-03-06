<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('can access admin books edit page', function () {
    // Create role and permission if they don't exist
    $role = Role::firstOrCreate(['name' => 'Super Admin']);
    $permission = Permission::firstOrCreate(['name' => 'access dashboard']);
    $role->givePermissionTo($permission);

    $user = User::factory()->create();
    $user->assignRole($role);

    $category = Category::factory()->create();
    $book = Book::factory()->create(['category_id' => $category->id]);

    $response = $this->actingAs($user)->get("/admin/books/{$book->slug}/edit");

    $response->assertStatus(200);
    $response->assertSee('Edit Book');
    $response->assertSee($book->title);
});

it('can access admin books create page', function () {
    // Create role and permission if they don't exist
    $role = Role::firstOrCreate(['name' => 'Super Admin']);
    $permission = Permission::firstOrCreate(['name' => 'access dashboard']);
    $role->givePermissionTo($permission);

    $user = User::factory()->create();
    $user->assignRole($role);

    $response = $this->actingAs($user)->get('/admin/books/create');

    $response->assertStatus(200);
    $response->assertSee('Add New Book');
});
