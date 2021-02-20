<?php

namespace App\Http\Controllers;

use App\product;
use App\cart;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
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
        $products = product::all();

        return view('product.index' , compact(('products')));

    }




    public function update(Request $request, product $product)
    {
        $request->validate([
            'qty' => 'required|numeric'
        ]);

        $cart = new Cart(session()->get('cart'));
        $cart->updateQty($product->id, $request->qty);
        session()->put('cart' , $cart);
        return redirect()->route('cart.show')->with('success' , 'Produktić updejtan');
    }


    public function destroy(product $product)
    {
        $cart = new Cart( session()->get('cart'));
        $cart->remove($product->id);

        if($cart->totalQty <= 0){
            session()->forget('cart');
        }else{
            session()->put('cart' , $cart);
        }

        return redirect()->route('cart.show')->with('success' , 'Maknuo si ga');

    }

    public function addToCart(product $product){

        if(session()->has('cart')) {
            $cart = new cart(session()->get('cart'));
        }
        else{
            $cart = new cart();
        }

        $cart->add($product);
        //dd($cart);
        session()->put('cart' , $cart);
        return redirect()->route('product.index')->with('success' , 'Dobar si');
    }


    public function showCart(){

        if(session()->has('cart')){
            $cart = new cart(session()->get('cart'));
        }
        else{
            $cart = null;
        }
        return view('cart.show' , compact('cart'));
    }




    ///////////

    public function checkout($amount){

        return view('cart.checkout' , compact('amount'));
    }




    public function charge(Request $request , $amount){

        //dd($request->amount);
        $charge = Stripe::charges()->create([
            'amount'=> $amount,
            'source'=> $request->stripeToken,
            'description'=> "Parice di ste!",
            'currency'=> 'USD',
        ]);

        $chargeId = $charge['id'];

        if($chargeId) {
            //save order in orders table <div class=""></div>

            auth()->user()->orders()->create([
                'cart' => serialize( session()->get('cart'))
            ]);

            //clear cart

            session()->forget('cart');
            return redirect()->route('store')->with('success' , "Payment was done!");
        }else{
            return redirect()->back();
        }
    }


} //kraj
