<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Products;
use \Illuminate\Database\QueryException;
use App\Types;
use App\Brands;

class ProductController extends Controller
{
    
    // public function deleteBrand(Request $request)
    // {
    //   try {
    //       $id = $request->id;
    //       $brands = Brands::find($id);
    //       $brands->delete();

    //   } catch (Exception $e) {
    //       return response()->json(['Status' => "Database Error"]);
    //   }

    // }

    
    public function search(Request $request)
    {

        //default value to empty string if no value is passed in

        $type = empty($request->type)? "": $request->type;
        $name = empty($request->name)? "": $request->name;
        $brand = empty($request->brand)? "": $request->brand;

        $product = Products::where('name', 'LIKE', '%'.$name.'%')
                            ->where('type', 'LIKE', '%'.$type.'%')
                            ->where('brand', 'LIKE', '%'.$brand.'%')
                            ->get();

        return response()->json($product);
        //return response()->json($data);
    }

    public function AddProduct(Request $request)
    {

        $searchType = empty($request->searchType)? "": $request->searchType;
        $searchName = empty($request->searchName)? "": $request->searchName;
        $searchBrand = empty($request->searchBrand)? "": $request->searchBrand;

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:products',
            'type' => 'required',
            'brand' => 'required',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'qty' => 'required|integer|min:0',
            'img' => 'required|image',
            'imgDetail' => 'required|image',

        ]);

        if($validator->fails())
        {
            return response()->json(['Status' => "Validation Error", "Message" => $validator->errors()]);
        }

        try
        {
            $product = new Products();
            $product->name = $request->name;
            $product->type = $request->type;
            $product->brand = $request->brand;
            $product->price = $request->price;
            $product->qty = $request->qty;
            $product->save();
            $id = $product->id;

        }catch (QueryException $e) 
        {
            return response()->json(['Status' => "Database Error", "Message" => $e->getMessage()]);
        }

        $db_name_1 = '/img/default.jpg';
        $db_name_2 = '/img/default.jpg';

        if($request->hasFile('img'))
        {
            $image = $request->img;
            $new_name_1 = $id.'_product'.'.'.$image->getClientOriginalExtension();
            $image->move(public_path('img'), $new_name_1);
            $db_name_1 = '/img/'.$new_name_1;
        }
        if($request->hasFile('imgDetail'))
        {
            $imgDetail = $request->imgDetail;
            $new_name_2 = $id.'_detail'.'.'.$imgDetail->getClientOriginalExtension();
            $imgDetail->move(public_path('img'), $new_name_2);
            $db_name_2 = '/img/'.$new_name_2;
        }

        DB::table('products')
            ->where('id', $id)
            ->update(['img' => $db_name_1, 'imgDetail' => $db_name_2]);


        // $searchProduct = Products::where('name', 'LIKE', '%'.$searchName.'%')
        //                     ->where('type', 'LIKE', '%'.$searchType.'%')
        //                     ->where('brand', 'LIKE', '%'.$searchBrand.'%')
        //                     ->get();


        // return response()->json(['Status' => "Success","Data" => $searchProduct]);

        return response()->json(['Status' => "Success","Data" => Types::all()]);
    }

    public function EditProduct(Request $request)
    {
        $searchType = empty($request->searchType)? "": $request->searchType;
        $searchName = empty($request->searchName)? "": $request->searchName;
        $searchBrand = empty($request->searchBrand)? "": $request->searchBrand;
        $id = $request->id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|',
            'type' => 'required',
            'brand' => 'required',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'qty' => 'required|integer|min:0',

        ]);

        if($validator->fails())
        {
            return response()->json(['Status' => "Validation Error", "Message" => $validator->errors()]);
        }

        try
        {
            $product = Products::find($id);
            $product->name = $request->name;
            $product->type = $request->type;
            $product->brand = $request->brand;
            $product->price = $request->price;
            $product->qty = $request->qty;
            $product->save();

        } catch (QueryException $e) {

            return response()->json(['Status' => "Database Error", "Message" => $e->getMessage()]);
        }

        if($request->hasFile('img'))
        {
            $image = $request->img;
            $new_name_1 = $id.'_product'.'.'.$image->getClientOriginalExtension();
            $image->move(public_path('img'), $new_name_1);
            $db_name_1 = '/img/'.$new_name_1;

            DB::table('products')
                ->where('id', $id)
                ->update(['img' => $db_name_1]);
        }
        if($request->hasFile('imgDetail'))
        {
            $imgDetail = $request->imgDetail;
            $new_name_2 = $id.'_detail'.'.'.$imgDetail->getClientOriginalExtension();
            $imgDetail->move(public_path('img'), $new_name_2);
            $db_name_2 = '/img/'.$new_name_2;

            DB::table('products')
                ->where('id', $id)
                ->update(['img' => $db_name_2]);
        }

        return response()->json(['Status' => "Success","Data" => Types::all()]);

    }


    public function AddType(Request $request)
    {


        $validator = Validator::make($request->all(), [
        'type' => 'required|unique:types',

        ]);


        if($validator->fails())
        {
            return response()->json(['Status' => "Validation Error", "Message" => $validator->errors()]);
        }



        try
        {

            $types = new Types();
            $types->type = $request->type;
            $types->save();


        } catch (QueryException $e) {

            return response()->json(['Status' => "Database Error", "Message" => $e->getMessage()]);
        }

        return response()->json(['Status' => "Success","Data" => Types::all()]);
    }

    public function AddBrand(Request $request)
    {

     
      
        $validator = Validator::make($request->all(), [
        'brand' => 'required|unique:brands',

        ]);

        if($validator->fails())
        {
            return response()->json(['Status' => "Validation Error", "Message" => $validator->errors()]);
        }

        try
        {

            $brands = new Brands();
            $brands->brand = $request->brand;
            $brands->save();
            $id = $brands->id;


        } catch (QueryException $e) {


            return response()->json(['Status' => "Database Error", "Message" =>  $e->getMessage()]);
        }


        return response()->json(['Status' => "Success","Data" => Brands::all()]);

    }



    public function check(Request $request)
    {
        // $product = new Products();
        // $product->name = $request->name;
        // $product->save();
        // $id = $product->id;
        // DB::table('products')
        //     ->where('id', $id)
        //     ->update(['img' => 'something', 'imgDetail' => 'somethingelse']);

    }


}
