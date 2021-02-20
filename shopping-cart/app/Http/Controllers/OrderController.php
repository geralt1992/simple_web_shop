<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $orders = auth()->user()->orders;

        $carts = $orders->transform( function($cart, $key){
            return unserialize($cart->cart);
        });

       // dd($carts);

       return view('order.index')->with('carts', $carts);
    }


}
