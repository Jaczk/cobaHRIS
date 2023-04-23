<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;

class CompanyController extends Controller
{
    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);

        $companyQuery = Company::with(['users'])->whereHas('users', function ($query) {
            $query->where('user_id', Auth::user()->id);
        });
        //Get Single Company
        if ($id) {
            $company = $companyQuery->find($id);

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

        //Get List Company
        $companies = $companyQuery;

        if ($name) {
            $companies->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $companies->paginate($limit),
            'Data list perusahaan berhasil diambil'
        );
    }

    public function create(CreateCompanyRequest $request)
    {
        try {
            //Upload Logo
            if ($request->file('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }
            //Create Company
            $company = Company::create([
                'name' => $request->name,
                'logo' => $path,
            ]);
            if (!$company) {
                throw new Exception('Data perusahaan gagal ditambahkan');
            }

            //Attach Company to User
            $user = User::find(Auth::user()->id);
            $user->companies()->attach($company->id);

            //Load User and Company
            $company->load('users');
            return ResponseFormatter::success(
                $company,
                'Data perusahaan berhasil ditambahkan'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error(
                $error->getMessage(),
                500
            );
        }
    }
    
    public function update(UpdateCompanyRequest $request, $id)
    {
        try {
            $company = Company::find($id);

            if (!$company){
                throw new Exception('Data perusahaan tidak ada');
            }

            //Upload Logo
            if ($request->file('logo')) {
                $path = $request->file('logo')->store('public/logos');
            }

            //Update Company
            $company->update([
                'name' => $request->name,
                'logo' => isset($path) ? $path : $company->logo ?? null,
            ]);

            //Load User and Company
            // $company->load('users');
            return ResponseFormatter::success(
                $company,
                'Data perusahaan berhasil diubah'
            );

        } catch (Exception $error) {
            return ResponseFormatter::error(
                $error->getMessage(),
                500
            );
        }
    }
}
