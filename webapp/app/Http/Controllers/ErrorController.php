<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// app/Http/Controllers/ErrorController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorController extends Controller
{
    public function notFound(Request $request)
    {
        // You can add custom logic here (logging, notifications, etc.)
        return response()->view('errors.404', [], 404);
    }

    
}
