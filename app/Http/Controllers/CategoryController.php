<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Http\Resources\UserResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
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
            $category = Category::all();

            return response()->json([
                'categorys' => CategoryController::collection($category),
            ], 200);
        }

        // Fetch paginated products if page_size and page_number are provided
        $category = Category::paginate($page_size, ['*'], 'page', $page_number);

        return response()->json([
            'products' => $category,
            'pagination' => [
                'current_page' => $category->currentPage(),
                'last_page' => $category->lastPage(),
                'per_page' => $category->perPage(),
                'total' => $category->total(),
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

        $CategoryItems = $request->CategoryItems;
        if (isset($CategoryItems)){
            foreach ($CategoryItems as $item){
                $category = Category::create(
                    [
                        'name' => [
                            'ar' => $item['ar_name'],
                            'en' => $item['en_name']
                        ],
                    ]
                );
                $category->save();
            }
        }
        return response()->json([
            'message' => 'succefully created'
        ],200);




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
            $category = Category::where('id',$id)->first();

            return response()->json([
                'products' => $category
            ],200);
        }catch (\Exception $exception){
            return response()->json([
                'error' => $exception->getMessage()
            ],500);
        }
    }

    public function update(Request  $request, $id)
    {
                $category = Category::find($id);
                if ($category) {
                    $category->update([
                        'name' => [
                            'ar' => $request['ar_name'],
                            'en' => $request['en_name']
                        ],
                    ]);
                }
             $category->save();

        return response()->json([
            'message' => 'succefully updated'
        ],200);

    }

    public function destroy($id)
    {
        try {
            $product = Category::find($id);
            $product->delete();
            return response()->json([
                'message' => 'succefully deleted'
            ],200);
        }catch (\Exception $exception){
            return $exception->getMessage();
        }
    }
}
