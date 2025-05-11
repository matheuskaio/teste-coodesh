<?php

namespace App\Domains\Product\Services;

use App\Domains\Product\Repositories\ProductRepository;

class ProductService
{
  public function __construct(
    protected ProductRepository $repo
  ) {}

  public function findByCode(string $code)
  {
    $product = $this->repo->findByCode($code);

    if (!$product) {
      throw new \Exception("Produto não encontrado", 404);
    }

    return $product;
  }

  public function markAsTrash(string $code)
  {
    $product = $this->findByCode($code);
    return $this->repo->update($product, ['status' => 'trash']);
  }


  public function update(string $code, array $data)
  {
    $product = $this->repo->findByCode($code);

    if (!$product) {
      throw new \Exception("Produto não encontrado", 404);
    }

    return $this->repo->update($product, $data);
  }

  public function paginate(int $perPage = 15)
  {
    return $this->repo->paginate($perPage);
  }
}
