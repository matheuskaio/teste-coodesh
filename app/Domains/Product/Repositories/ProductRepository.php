<?php

namespace App\Domains\Product\Repositories;

use App\Models\Product;

class ProductRepository
{
  public function findByCode(string $code): ?Product
  {
    return Product::where('code', $code)->first();
  }

  public function update(Product $product, array $data): Product
  {
    $product->update($data);
    return $product;
  }

  public function paginate(int $perPage = 15)
  {
    return Product::paginate($perPage);
  }
}
