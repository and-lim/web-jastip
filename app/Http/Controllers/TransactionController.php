<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Item;
use App\Models\RequestItem;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    function addRequestItem(Request $request)
    {
        $filename = $request->file('request_image')->getClientOriginalName();
        $generate_file = time() . '_' . $filename;

        $path = $request->file('request_image')->storeAs('public/', $generate_file);


        $request_items = RequestItem::create([
            'requester_id' => auth()->user()->id,
            'request_name' => $request->request_name,
            'request_category' => $request->request_category,
            'request_brand' => $request->request_brand,
            'request_image' => $generate_file,
            'request_description' => $request->request_description,
            'request_price' => $request->request_price,
            'request_quantity' => $request->request_quantity,
            'request_weight' => $request->request_weight,
            'trip_id' => $request->trip_id
        ]);

        $cart_request = Cart::create([
            'user_id' => auth()->user()->id,
            'request_id' => $request_items->id,
            // 'item_id' => null,
            'cart_item_quantity' => $request_items->request_quantity,
        ]);

        return redirect('/trip-detail/' . $request->trip_id);
        // return back();
    }

    function addToCart(Request $request)
    {
        $find = Cart::where('user_id', auth()->user()->id)->where('item_id', $request->item_id)->first();

        if ($find) {
            $find->cart_item_quantity = $request->item_quantity;
            $find->save();
        } else {

            $add_cart = Cart::create([
                'user_id' => auth()->user()->id,
                'item_id' => $request->item_id,
                'cart_item_quantity' => $request->item_quantity
            ]);
        }


        return back();
    }

    function viewCart()
    {
        $cart_item = DB::table('carts')
            ->join('items', 'carts.item_id', 'items.id')
            ->select('items.*', 'carts.*')
            ->where('carts.user_id', auth()->user()->id)
            ->where('carts.item_id', '<>', 'NULL')
            ->where('carts.cart_status', 'unpaid')
            ->get();

        $cart_request = DB::table('carts')
            ->join('request_items', 'carts.request_id', 'request_items.id')
            ->select('request_items.*', 'carts.*')
            ->where('request_items.request_status', '<>', 'rejected')
            ->where('carts.user_id', auth()->user()->id)
            ->where('carts.cart_status', 'unpaid')
            ->get();

        $check_status = DB::table('carts')
            ->join('request_items', 'carts.request_id', 'request_items.id')
            ->select('request_items.request_status')
            ->where('request_items.request_status', 'waiting approval')
            ->where('carts.user_id', auth()->user()->id)
            ->first();


        return view('cart', compact('cart_item', 'cart_request', 'check_status'));
    }

    function deleteItemCart(Request $request)
    {
        $cart_item = DB::table('carts')
            ->where('carts.item_id', $request->item_id)
            ->delete();

        return back();
    }
    function deleteRequestCart(Request $request)
    {
        $cart_item = DB::table('carts')
            ->where('carts.request_id', $request->request_id)
            ->delete();

        return back();
    }

    function pay(Request $request)
    {

        $paid_validation = auth()->user()->balance;
        $rules = [
            'total_pay' => 'lte:' . $paid_validation
        ];
        $messages = [
            'total_pay.lte' => 'Your balance is not sufficient'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }


        $pay_cart = Cart::where('user_id', auth()->user()->id)->get();
        
        $transactions = Transaction::create([
            'user_id' => auth()->user()->id,
            'shipping_type_id' => $request->shipping_id,
            'total_paid' => $request->total_pay
        ]);

        foreach($pay_cart as $cart) {
            $cart->cart_status = 'paid';
            $cart->save();

            $item = Item::find($cart->item_id);

        
            $transaction_detail = TransactionDetail::create([
                'transaction_id' => $transactions->id,
                'item_id' => $cart->item_id,
                'request_id' => $cart->request_id,
                'quantity' => $cart->cart_item_quantity,
                'price' => $item->item_price * $cart->cart_item_quantity,
                'total' => $item->item_display_price * $cart->cart_item_quantity,
                'profit' => ($item->item_display_price * $cart->cart_item_quantity) - ($item->item_price * $cart->cart_item_quantity)
            ]);


        }

    }
}
