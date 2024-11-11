<?php
namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\Product;
use Carbon\Carbon;

class APIController extends Controller
{

    private function returnError(string $err)
    {
        return response()->json(array(
            'err' => $err
        ));
    }

    public function incrementBasket(Request $request)
    {
        $input = $request->all();
        $user_id = $input['user_id'];
        $json = array();
        $checkUser = DB::table('users')->where('id', $user_id)->first();
        if (! $checkUser) {
            return $this->returnError('noUser');
        }
        $product_id = $input['product_id'];
        $checkProduct = Product::whereId($product_id)->first();
        if (! $checkProduct) {
            return $this->returnError('noProduct');
        }
        $json['wallet'] = $checkUser->wallet;
        $json['total'] = 0;

        $userBasket = DB::table('basket')->join('products', 'products.id', '=', 'basket.products_id')
            ->where('user_id', $user_id)
            ->select('basket.*', 'products.price as product_price', 'products.count as product_count')
            ->get();

        $product_price = $checkProduct->price;
        $product_count = 0;
        $wallet = $checkUser->wallet;

        foreach ($userBasket as $product) {
            $json['total'] += $product->count * $product->product_price;
            if ($product->products_id == $product_id)
                $product_count = $product->count;
        }
        if ($wallet < $json['total'] + $product_price) {
            return $this->returnError('tooBigBasket');
        }
        $now = Carbon::now();
        DB::table('basket')->updateOrInsert([
            'user_id' => $user_id,
            'products_id' => $product_id
        ], [
            'count' => $product_count + 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $json['total'] += $product_price;
        $json['product_id'] = $product_id;
        $json['product_name'] = $checkProduct->product_name;
        $json['product_price'] = $product_price;

        return response()->json($json);
    }

    public function decrementBasket(Request $request)
    {
        $input = $request->all();
        $user_id = $input['user_id'];
        $json = array();
        $checkUser = DB::table('users')->where('id', $user_id)->first();
        if (! $checkUser) {
            return $this->returnError('noUser');
        }
        $product_id = $input['product_id'];
        $checkProduct = Product::whereId($product_id)->first();
        if (! $checkProduct) {
            return $this->returnError('noProduct');
        }
        $json['wallet'] = $checkUser->wallet;
        $json['total'] = 0;

        $userBasket = DB::table('basket')->join('products', 'products.id', '=', 'basket.products_id')
            ->where('user_id', $user_id)
            ->select('basket.*', 'products.price as product_price', 'products.count as product_count')
            ->get();

        $product_price = $checkProduct->price;
        $product_count = 0;

        foreach ($userBasket as $product) {
            $json['total'] += $product->count * $product->product_price;
            if ($product->products_id == $product_id) {
                $product_count = $product->count;
                if ($product_count == 1) {
                    DB::table('basket')->where('user_id', $user_id)
                        ->where('products_id', $product_id)
                        ->delete();
                } else {
                    DB::table('basket')->where('user_id', $user_id)
                        ->where('products_id', $product_id)
                        ->decrement('count', 1);
                }
            }
        }

        $json['total'] -= $product_price;
        $json['product_id'] = $product_id;
        $json['product_name'] = $checkProduct->product_name;
        $json['product_price'] = $product_price;

        return response()->json($json);
    }

    public function clearBasket(Request $request)
    {
        $input = $request->all();
        $user_id = $input['user_id'];
        $json = array();
        $checkUser = DB::table('users')->where('id', $user_id)->first();
        if (! $checkUser) {
            return $this->returnError('noUser');
        }

        $json['status'] = 1;
        DB::table('basket')->where('user_id', $user_id)->delete();

        return response()->json($json);
    }
}

?>