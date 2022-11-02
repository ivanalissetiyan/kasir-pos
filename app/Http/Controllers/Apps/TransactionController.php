<?php

namespace App\Http\Controllers\Apps;

use Inertia\Inertia;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index()
    {
        //get cart
        $carts = Cart::with('product')->where('cashier_id', auth()->user()->id)->latest()->get();

        // Get all customers
        $customers = Customer::latest()->get();

        return Inertia::render('Apps/Transaction/Index', [
            'carts'         => $carts,
            'carts_total'   => $carts->sum('price'),
            'customers'     => $customers
        ]);
    }

    public function searchProduct(Request $request)
    {
        // Find product by barcode
        $product = Product::where('barcode', $request->barcode)->first();

        if ($product) {
            return response()->json([
                'success'   => true,
                'data'      => $product
            ]);
        }

        return response()->json([
            'success'   => false,
            'data'      => null
        ]);
    }

    public function addToCart(Request $request)
    {
        // Check stock product
        if (Product::whereId($request->product_id)->first()->stock < $request->qty) {

            // Redirect
            return redirect()->back()->with('error', 'Stock Product Habis!.');
        }

        // Check Cart
        $cart = Cart::with('product')
            ->where('product_id', $request->product_id)
            ->where('cashier_id', auth()->user()->id)
            ->first();

        if ($cart) {
            // Increment QTY
            $cart->increment('qty', $request->qty);

            // Sum price * quantity
            $cart->price = $cart->product->sell_price * $cart->qty;
            $cart->save();
        } else {
            // Insert cart
            Cart::create([
                'cashier_id'    => auth()->user()->id,
                'product_id'    => $request->product_id,
                'qty'           => $request->qty,
                'price'         => $request->sell_price * $request->qty,
            ]);
        }
        return redirect()->route('apps.transaction.index')->with('success', 'Product Berhasil Ditambahkan');
    }

    public function destroyCart(Request $request)
    {
        // Find cart by id
        $cart = Cart::with('product')
            ->whereId($request->cart_id)
            ->first();

        //Delete Cart
        $cart->delete();

        return redirect()->back()->with('success', 'Product Berhasil DiHapus');
    }

    public function store(Request $request)
    {
        $length = 10;
        $random = '';
        for ($i = 0; $i < $length; $i++) {
            $random .= rand(0, 1) ? rand(0, 9) : chr(rand(ord('a'), ord('z')));
        }

        // Generate no invoice
        $invoice = 'TRX-' . Str::upper($random);

        // Insert Transaction
        $transaction = Transaction::create([
            'cashier_id'    => auth()->user()->id,
            'customer_id'   => $request->customer_id,
            'invoice'       => $invoice,
            'cash'          => $request->cash,
            'change'        => $request->change,
            'discount'      => $request->discount,
            'grand_total'   => $request->grand_total,
        ]);

        // Get Carts
        $carts = Cart::where('cashier_id', auth()->user()->id)->get();

        // Insert Transaction Details
        foreach ($carts as $cart) {
            $transaction->details()->create([
                'transaction_id'    => $transaction->id,
                'product_id'        => $cart->product_id,
                'qty'               => $cart->qty,
                'price'             => $cart->price,
            ]);

            // Get Price
            $total_buy_price = $cart->product->buy_price * $cart->qty;
            $total_sell_price = $cart->product->sell_price * $cart->qty;

            // get profits
            $profits = $total_sell_price - $total_buy_price;

            // Insert Profits
            $transaction->profits()->create([
                'transaction_id'    => $transaction->id,
                'total'    => $profits,
            ]);

            // Update stock Product
            $product = Product::find($cart->product_id);
            $product->stock = $product->stock - $cart->qty;
            $product->save();
        }

        // Delete Carts
        Cart::where('cashier_id', auth()->user()->id)->delete();

        return response()->json([
            'success' => true,
            'data'  => $transaction,
        ]);
    }

    // Print
    public function print(Request $request)
    {
        // Get Transaction
        $transaction = Transaction::with('details.product', 'cashier', 'customer')->where('invoice', $request->invoice)->firstOrFail();

        // Return View
        return view('print.nota', compact('transaction'));
    }
}
