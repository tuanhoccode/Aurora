<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ErrorController extends Controller
{
    public function notFound(){
        return response()->view('Client.errors.404', [], 404);//Trả về view 404 và mã lỗi 404
    }
}
