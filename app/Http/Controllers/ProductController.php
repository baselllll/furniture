<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Product::create(['']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
