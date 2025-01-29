<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    public function showDocumentation(){
        return view("documentation.index");
    }
}
