<?php

namespace App\Http\Controllers\API;

use App\Models\Company;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 10);

        if ($id) {
            $company = Company::with(['users'])->find($id);

            if ($company) {
                return ResponseFormatter::success(
                    $company,
                    'Data perusahaan berhasil diambil'
                );
            }
            return ResponseFormatter::error(
                null,
                'Data perusahaan tidak ada',
                404
            );
        }

    }
}
