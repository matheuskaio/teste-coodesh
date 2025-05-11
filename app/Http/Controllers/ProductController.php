<?php

namespace App\Http\Controllers;

use App\Domains\Product\Services\ProductService;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $service
    ) {}

    public function update(UpdateProductRequest $request, $code)
    {
        try {
            $product = $this->service->update($code, $request->validated());

            return response()->json([
                'message' => 'Produto atualizado com sucesso!',
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        $products = $this->service->paginate($perPage);

        return response()->json([
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    public function show(string $code)
    {
        try {
            $product = $this->service->findByCode($code);

            return response()->json([
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }

    public function destroy(string $code)
    {
        try {
            $product = $this->service->markAsTrash($code);

            return response()->json([
                'message' => 'Produto movido para lixeira',
                'data' => $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }
}



// class ProductController extends Controller
// {

//     public function index(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'per_page' => 'numeric|min:1|max:100',
//             'order_by' => 'in:code,product_name,created_at,imported_t',
//             'sort' => 'in:asc,desc'
//         ]);

//         if ($validator->fails()) {
//             return response()->json(['errors' => $validator->errors()], 422);
//         }

//         $orderBy = $request->get('order_by', 'created_at');
//         $sort = $request->get('sort', 'desc');
//         $perPage = $request->get('per_page', 15);

//         $products = Product::orderBy($orderBy, $sort)->paginate($perPage);

//         return response()->json([
//             'data' => $products->items(),
//             'meta' => [
//                 'current_page' => $products->currentPage(),
//                 'last_page' => $products->lastPage(),
//                 'per_page' => $products->perPage(),
//                 'total' => $products->total(),
//             ]
//         ]);
//     }

//     public function show($code)
//     {
//         $product = Product::where('code', request('code'))->first();

//         if (!$product) {
//             return response()->json(['message' => 'Produto não encontrado'], 404);
//         }

//         return response()->json($product, 200);
//     }

//     public function destroy($code)
//     {
//         $product = Product::where('code', $code)->first();

//         if (!$product) {
//             return response()->json(['message' => 'Produto não encontrado'], 404);
//         }

//         $product->status = 'trash';
//         $product->save();

//         return response()->json(['message' => 'Status alterado com sucesso!'], 200);
//     }

//     public function update(Request $request, $code)
//     {
//         $product = Product::where('code', $code)->first();

//         if (!$product) {
//             return response()->json(['message' => 'Produto não encontrado'], 404);
//         }

//         $validator = Validator::make($request->all(), [
//             'status' => 'in:draft,trash,published',
//             'product_name' => 'string',
//             'quantity' => 'string',
//             'brands' => 'string',
//             'categories' => 'string',
//             'labels' => 'string',
//             'cities' => 'string',
//             'purchase_places' => 'string',
//             'stores' => 'string',
//             'ingredients_text' => 'string',
//             'traces' => 'string',
//             'serving_size' => 'string',
//             'serving_quantity' => 'numeric',
//             'nutriscore_score' => 'numeric',
//             'nutriscore_grade' => 'string',
//         ]);

//         if ($validator->fails())
//             return response()->json($validator->errors(), 422);

//         $product->update($request->all());

//         return response()->json([
//             'message' => 'Produto atualizado com sucesso!',
//             'data' => $product
//         ]);
//     }
// }
