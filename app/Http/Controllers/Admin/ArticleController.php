<?php
/**
 * Created by PhpStorm.
 * User: angai
 * Date: 16.11.2017
 * Time: 20:41
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    public function getView()
    {
        return view("admin.article",["pageName"=>"article"]);
    }
}