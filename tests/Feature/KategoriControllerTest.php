<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class KategoriControllerTest extends TestCase
{
    // use RefreshDatabase;
    
    /**
     * A basic feature test example.
     */
    public function testStoreSuccesscfully() : void
    {

        $data = [
            'nama_kategori' => 'Test barang',
        ];
        $response = $this->post('/admin/kategori', $data);

        $response->assertRedirect('/admin/kategori');
        // $response->assertStatus(200);

        $this->assertDatabaseHas('kategori', $data);
    }
}
