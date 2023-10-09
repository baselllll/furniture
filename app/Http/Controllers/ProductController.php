<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page_size = $request->page_size ?? 15;
        $page_number = $request->page_number ?? 1;

        if ($page_size === 'all' && $page_number === 'all') {
            // Fetch all products without pagination
            $products = Product::with('users','category')->get();

            return response()->json([
                'products' => ProductResource::collection($products),
            ], 200);
        }

        // Fetch paginated products if page_size and page_number are provided
        $products = Product::with('users')->paginate($page_size, ['*'], 'page', $page_number);

        return response()->json([
            'products' => ProductResource::collection($products->items()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ], 200);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $loggedInUser = Auth::user();
            $setOfProducts = $request->productsItems;
            if (isset($setOfProducts)) {
                foreach ($setOfProducts as $item) {
                    $item['ar_status']  =  isset($item['ar_status']) ? $item['ar_status'] : '';
                    $item['en_status'] = isset($item['en_status']) ? $item['en_status'] : '';
                    $item['category_id']  =  isset($item['category_id']) ? $item['category_id'] : '';
                    $item['ar_status'] = "";
                    $item['en_status'] = "";
                    $product = Product::create(
                        [
                            'price' => $item['price'],
                            'quantity' => $item['quantity'],
                            'discount' => $item['discount'],
                            'featured' => $item['featured'],
                            'category_id' => $item['category_id'],
                            'name' => [
                                'ar' => $item['ar_name'],
                                'en' => $item['en_name']
                            ],
                            'brand' => [
                                'ar' => $item['ar_brand'],
                                'en' => $item['en_brand']
                            ],
                            'type' => [
                                'ar' => $item['ar_type'],
                                'en' => $item['en_type']
                            ],
                            'status' => [
                                'ar' => $item['ar_status'],
                                'en' => $item['en_status']
                            ],
                            'description' => [
                                'ar' => $item['ar_description'],
                                'en' => $item['en_description']
                            ],

                        ]
                    );
                    if(isset($item['image'])){
                        $item->addMedia($item['image'])
                            ->preservingOriginal()
                            ->toMediaCollection('image');
                    }
                    $loggedInUser->products()->save($product);


                }
            } else {
                isset($request['ar_status']) ? $request['ar_status'] : '';
                isset($request['ar_status']) ? $request['en_status'] : '';
                $request['ar_status'] = "";
                $request['en_status'] = "";
                $product = Product::create(
                    [
                        'price' => $request['price'],
                        'quantity' => $request['quantity'],
                        'discount' => $request['discount'],
                        'featured' => $request['featured'] == false ? 0 : 1,
                        'category_id' => isset($request['category_id']) ? $request['category_id'] : '',
                        'name' => [
                            'ar' => $request['ar_name'],
                            'en' => $request['en_name']
                        ],
                        'brand' => [
                            'ar' => $request['ar_brand'],
                            'en' => $request['en_brand']
                        ],
                        'type' => [
                            'ar' => $request['ar_type'],
                            'en' => $request['en_type']
                        ],
                        'status' => [
                            'ar' => $request['ar_status'],
                            'en' => $request['en_status']
                        ],
                        'description' => [
                            'ar' => $request['ar_description'],
                            'en' => $request['en_description']
                        ],

                    ]
                );
                if (isset($request['image'])) {
                    $product->addMedia($request['image'])
                        ->preservingOriginal()
                        ->toMediaCollection('image');
                }
                $loggedInUser->products()->save($product);
            }
            return response()->json([
                'status' => 'successfully created'
            ],200);
        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json([
                'error' => $exception->getMessage()
            ],500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $product = new ProductResource(Product::with('category')->where('id',$id)->first());

            return response()->json([
                'products' => $product
            ],200);
        }catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage()
            ],500);
        }
    }
    public function ProductFeatured()
    {
        try {
            $products = ProductResource::collection(Product::whereFeatured(true)->paginate(15));
            return response()->json([
                'products' => $products
            ],200);
        }catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage()
            ],500);
        }
    }
    public function getproductsBy($selectType,Request $request)
    {
        $request['selectType']= $selectType;
        $sended_value = $request->sended_value;
        $category_ids = $request->category_ids;
        try {
            $validator = Validator::make($request->all(), [
                'selectType' => 'in:name,priceLtH,priceHtL,type,brand,category',
                'sended_value'=>'sometimes',
                'category_ids'=>'array|required_if:selectType,==,category'
            ]);
            if ($validator->fails()) {
                return response()->json(["error" => $validator->errors()], 400);
            }
            $query = Product::query();

            $query->when($selectType == 'name', function ($q) use ($sended_value) {
                $q->where('name','like',$sended_value);
            });
            $query->when($selectType == 'category', function ($q) use ($category_ids) {
                $q->whereIn('category_id', $category_ids);
            });
            $query->when($selectType == 'priceLtH', function ($q) {
                $q->orderBy('price', 'asc');
            });
            $query->when($selectType == 'priceHtL', function ($q) {
                $q->orderBy('price', 'desc');
            });
            $query->when($selectType == 'type', function ($q) use ($sended_value) {
                $q->where('type','=',$sended_value);
            });
            $query->when($selectType == 'brand', function ($q) use ($sended_value) {
                $q->where('brand','=',$sended_value);
            });
            $products = $query->paginate(15);
            $products = ProductResource::collection($products);

            return response()->json([
                'data' => $products
            ],200);
        }catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage()
            ],200);
        }
    }
    public function ProductsLoginedUsers()
    {
        try {
            $user = User::with('products')->where('id',auth()->user()->id)->first();
            $product = new UserResource($user);
            return response()->json([
                'products' => $product
            ],200);
        }catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage()
            ],200);
        }
    }


    public function update(Request  $request, $id)
    {
        $loggedInUser = Auth::user();
        $product = Product::find($id);
        if ($product) {
            $product->update([
                'price' => $request['price'],
                'quantity' => $request['quantity'],
                'discount' => $request['discount'],
                'category_id'=>$request['category_id'],
                'name' => $request['name'],
                'brand' => $request['brand'],
                'type' => $request['type'],
                'status' => $request['status'],
                'featured' => $request['featured'],
            ]);
        }
        $product->clearMediaCollection('image');
        $product->addMedia($request['image'])
            ->preservingOriginal()
            ->toMediaCollection('image');
        $loggedInUser->products()->save($product);

        return response()->json([
            'message' => 'succefully updated'
        ],200);

    }

    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            $product->delete();
            return response()->json([
                'message' => 'succefully deleted'
            ],200);
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }
}
