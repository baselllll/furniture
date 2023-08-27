<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = ProductResource::collection(Product::with('users')->paginate(15));
        return response()->json([
            'products' => $products
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $loggedInUser = Auth::user();
        $setOfProducts = $request->productsItems;
        if (isset($setOfProducts)){
            foreach ($setOfProducts as $item){
                $product = Product::create(
                    [
                        'price' => $item['price'],
                        'quantity'=> $item['quantity'],
                        'discount' => $item['discount'],
                        'name' => $item['name'],
                        'brand' => $item['brand'],
                        'type' => $item['type'],
                        'status' => $item['status'],
                        'featured' => $item['featured'],
                    ]
                );

                $product->addMedia($item['image'])
                    ->preservingOriginal()
                    ->toMediaCollection('image');
                $loggedInUser->products()->save($product);
            }
        }
        return response()->json([
            'message' => 'succefully created'
        ],200);


//        //update
//        $product->clearMediaCollection('image');
//        //Config::set('media-library.prefix', 'uploads/product');
//        $product->addMedia($file)
//            ->preservingOriginal()
//            ->toMediaCollection('image');


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
            $product = new ProductResource(Product::where('id',$id)->first());

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
        try {
            $validator = Validator::make($request->all(), [
                'selectType' => 'in:name,priceLtH,priceHtL,type,brand',
                'sended_value'=>'required'
            ]);
            if ($validator->fails()) {
                return response()->json(["error" => $validator->errors()], 400);
            }


            $query = Product::query();

            $query->when($selectType == 'name', function ($q) use ($sended_value) {
                $q->where('name','like',$sended_value);
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
