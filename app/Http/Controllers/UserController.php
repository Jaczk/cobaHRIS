<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use App\Helpers\ResponseFormatter;

class UserController extends Controller
{
    public function index()
    {
        try {
            $employees = Employee::all();
            return ResponseFormatter::success($employees, 'Data retrieved successfully');
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th->getMessage(), 'Data retrieval failed', 500);
        }
    }
}
