<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class BasketController extends Controller
{
    public function getView()
    {
        return view("client.basket");
    }
}