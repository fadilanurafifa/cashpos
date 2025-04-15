<?php

namespace Tests\Feature;

use App\Models\User;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash as FacadesHash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    // use RefreshDatabase;

    public function testLoginSuccessfully()
    {
        $user = User::create([
          'name' => 'Admin',
            'email' => 'admin@caffe.com',
            'password' => FacadesHash::make('password'),
            'role' => 'admin',
            'status' => 'aktif'
        ]);

        $credentials = [
            'email' => 'admin@caffe.com',
            'password' => 'password',
        ];

        $response = $this->post('admin/login', $credentials);
        $response->assertStatus(302);
        // $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }
}
