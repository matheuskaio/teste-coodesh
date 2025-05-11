<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase; // Limpa o banco a cada teste

    /** @test */
    public function deve_retornar_um_produto_existente_por_code()
    {
        // Arrange: cria um produto fake no banco
        $product = Product::factory()->create([
            'code' => '1234567890123',
            'product_name' => 'Produto de Teste'
        ]);

        // Act: faz a requisição GET na rota da API
        $response = $this->getJson("/api/products/{$product->code}");

        // Assert: verifica o resultado
        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'code' => '1234567890123',
                    'product_name' => 'Produto de Teste'
                ]
            ]);
    }

    /** @test */
    public function deve_retornar_erro_404_quando_produto_nao_existir()
    {
        // Act: faz a requisição com um code inexistente
        $response = $this->getJson('/api/products/0000000000000');

        // Assert
        $response
            ->assertStatus(404)
            ->assertJson([
                'error' => 'Produto não encontrado'
            ]);
    }

    /** @test */
    public function deve_atualizar_um_produto_existente()
    {
        // Arrange
        $product = \App\Models\Product::factory()->create([
            'code' => '9999999999999',
            'product_name' => 'Nome Antigo',
            'status' => 'draft',
        ]);

        $payload = [
            'product_name' => 'Nome Novo Atualizado',
            'status' => 'published',
            'quantity' => '1L',
        ];

        // Act
        $response = $this->putJson("/api/products/{$product->code}", $payload);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Produto atualizado com sucesso!',
                'data' => [
                    'code' => '9999999999999',
                    'product_name' => 'Nome Novo Atualizado',
                    'status' => 'published',
                    'quantity' => '1L',
                ]
            ]);

        // Optional: assert directly in the DB
        $this->assertDatabaseHas('products', [
            'code' => '9999999999999',
            'product_name' => 'Nome Novo Atualizado',
            'status' => 'published',
        ]);
    }

    /** @test */
    public function deve_retornar_erro_404_ao_atualizar_produto_inexistente()
    {
        $payload = [
            'product_name' => 'Novo Nome',
            'status' => 'published',
        ];

        $response = $this->putJson('/api/products/0000000000000', $payload);

        $response
            ->assertStatus(404)
            ->assertJson([
                'error' => 'Produto não encontrado'
            ]);
    }

    /** @test */
    public function deve_retornar_erro_422_ao_atualizar_com_dados_invalidos()
    {
        $product = Product::factory()->create([
            'code' => '8888888888888',
        ]);

        $payload = [
            'status' => 'ativo', // valor inválido para enum
            'serving_quantity' => 'texto', // deveria ser número
        ];

        $response = $this->putJson("/api/products/{$product->code}", $payload);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['status', 'serving_quantity']);
    }

    /** @test */
    public function deve_marcar_um_produto_como_trash()
    {
        $product = Product::factory()->create([
            'code' => '7777777777777',
            'status' => 'draft',
        ]);

        $response = $this->deleteJson("/api/products/{$product->code}");

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Produto movido para lixeira',
                'data' => [
                    'code' => '7777777777777',
                    'status' => 'trash',
                ]
            ]);

        $this->assertDatabaseHas('products', [
            'code' => '7777777777777',
            'status' => 'trash',
        ]);
    }

    /** @test */
    public function deve_retornar_erro_404_ao_deletar_produto_inexistente()
    {
        $response = $this->deleteJson('/api/products/0000000000000');

        $response
            ->assertStatus(404)
            ->assertJson([
                'error' => 'Produto não encontrado'
            ]);
    }
}
