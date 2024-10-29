<?php
namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
use Carbon\Carbon;

class PageController extends Controller
{

    public function show(): View
    {
        return view('main');
    }

    public function basket(): View
    {
        return view('basket');
    }

    private function returnError(string $err)
    {
        return response()->json(array(
            'err' => $err
        ));
    }

    public function getProducts(Request $request)
    {
        $input = $request->all();
        $json = array();
        $products = DB::table('products')->get();
        $json['products'] = [];
        foreach ($products as $product) {
            $json['products'][] = [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'product_price' => $product->price,
            ];
        }
        return response()->json($json);
    }

    public function getBasket(Request $request)
    {
        $input = $request->all();
        $user_id = $input['user_id'];
        $json = array();
        $checkUser = DB::table('users')->where('id', $user_id)->first();
        if (! $checkUser) {
            return $this->returnError('noUser');
        }
        $json['wallet'] = $checkUser->wallet;
        $json['total'] = 0;

        $userBasket = DB::table('basket')->join('products', 'products.id', '=', 'basket.products_id')
            ->where('user_id', $user_id)
            ->select('basket.*', 'products.price as product_price', 'products.product_name')
            ->get();

        $json['products'] = array();
        foreach ($userBasket as $product) {
            $prData = array(
                'id' => $product->id,
                'product_id' => $product->products_id,
                'product_name' => $product->product_name,
                'product_price' => $product->product_price,
                'count' => $product->count
            );
            $json['total'] += $product->count * $product->product_price;
            array_push($json['products'], $prData);
        }

        return response()->json($json);
    }

    public function updateBasket(Request $request)
    {
        $input = $request->all();
        $user_id = $input['user_id'];
        $json = array();
        $checkUser = DB::table('users')->where('id', $user_id)->first();
        if (! $checkUser) {
            return $this->returnError('noUser');
        }

        $products = $input['products'];
        $prIds = array();
        $prMap = array();
        $total = 0;
        $wallet = $checkUser->wallet;
        if (! empty($products)) {
            foreach ($products as $product) {
                if (! empty($product['id']) && ! empty($product['count']) && $product['count'] > 0) {
                    $prIds[] = $product['id'];
                    $prMap[$product['id']] = $product;
                }
            }
        }
        $checkProducts = DB::table('products')->whereIn('id', $prIds)->get();
        if (count($checkProducts) != count($prIds)) {
            return $this->returnError('wrongProductIds');
        }
        $now = Carbon::now();
        $basketProducts = [];
        foreach ($checkProducts as $product) {
            $reqPr = $prMap[$product->id];
            $total += $reqPr['count'] * $product->price;
            $basketProducts[] = [
                'user_id' => $user_id,
                'products_id' => $product->id,
                'count' => $reqPr['count'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        if ($total > $wallet) {
            return $this->returnError('tooBigBasket');
        }

        DB::table('basket')->where('user_id', $user_id)->delete();
        DB::table('basket')->insert($basketProducts);

        $json['product_ids'] = $prIds;
        $json['total'] = $total;

        return response()->json($json);
    }

    public function purchase(Request $request)
    {
        $input = $request->all();
        $user_id = $input['user_id'];
        $json = array();
        $checkUser = DB::table('users')->where('id', $user_id)->first();
        if (! $checkUser) {
            return $this->returnError('noUser');
        }

        $userBasket = DB::table('basket')->join('products', 'products.id', '=', 'basket.products_id')
            ->where('user_id', $user_id)
            ->select('basket.*', 'products.price as product_price', 'products.product_name', 'products.count as product_count')
            ->get();
        if (count($userBasket) === 0) {
            return $this->returnError('noBasket');
        }

        $maxPerProduct = 5;
        $json['wallet'] = $checkUser->wallet;
        $json['total'] = 0;
        $now = Carbon::now();
        $json['products'] = array();
        $purchases = array();
        foreach ($userBasket as $product) {
            $count = $product->count;
            if ($count > $maxPerProduct)
                $count = $maxPerProduct;
            if ($count > $product->product_count)
                $count = $product->product_count;
            $inStoreCount = $product->product_count - $count;
            $left = DB::table('basket')->where('user_id', '!=', $user_id)
                ->where('products_id', $product->products_id)
                ->count();
            if ($inStoreCount < $left) {
                $count -= $left - $inStoreCount;
                $inStoreCount = $product->product_count - $count;
            }
            $prData = array(
                'id' => $product->id,
                'product_id' => $product->products_id,
                'product_name' => $product->product_name,
                'product_price' => $product->product_price,
                'purchased' => $product->count,
                'count' => $count,
                'stored' => $inStoreCount,
                'left' => $left
            );
            $json['total'] += $count * $product->product_price;
            array_push($json['products'], $prData);
        }

        if ($json['total'] > 0) {
            $transaction = [
                'user_id' => $user_id,
                'total' => $json['total'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $transactionId = DB::table('transactions')->insertGetId($transaction);

            foreach ($json['products'] as $pr) {
                $purchase = array(
                    'user_id' => $user_id,
                    'products_id' => $pr['product_id'],
                    'count' => $pr['count'],
                    'price' => $pr['product_price'],
                    'transactions_id' => $transactionId,
                    'created_at' => $now,
                    'updated_at' => $now,
                );
                DB::table('products')->where('id', $pr['product_id'])->decrement('count', $pr['count']);
                array_push($purchases, $purchase);
            }

            DB::table('purchases')->insert($purchases);
            $json['wallet'] -= $json['total'];
            DB::table('users')->where('id', $user_id)->update([
                'wallet' => $json['wallet']
            ]);
            DB::table('basket')->where('user_id', $user_id)->delete();
        }

        return response()->json($json);
    }

    public function getCSRF()
    {
        $json = array(
            'token' => csrf_token()
        );
        return response()->json($json);
    }
}

?>