<?php

namespace App\Http\Controllers\API;

use Exception;
use Illuminate\Http\Request;
use App\Models\Responsibility;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateResponsibilityRequest;

class ResponsibilityController extends Controller
{
    
    public function create(CreateResponsibilityRequest $request)
    {
        try {
            //Create responsibility
            $responsibility = Responsibility::create([
                'name' => $request->name,
                'role_id' => $request->roles_id,
            ]);
            if (!$responsibility) {
                throw new Exception('Data responsibility gagal ditambahkan');
            }
            return ResponseFormatter::success(
                $responsibility,
                'Data responsibility berhasil ditambahkan'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error(
                $error->getMessage(),
                500
            );
        }
    }

    public function fetch(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $limit = $request->input('limit', 10);
        $role_id = $request->input('role_id');

        $responsibilityQuery = responsibility::query();
        //Get Single responsibility
        if ($id) {
            $responsibility = $responsibilityQuery->find($id);

            if ($responsibility) {
                return ResponseFormatter::success(
                    $responsibility,
                    'Data responsibility berhasil diambil'
                );
            }
            return ResponseFormatter::error(
                null,
                'Data responsibility tidak ada',
                404
            );
        }

        //Get List responsibility
        $responsibilities = $responsibilityQuery;

        if ($name) {
            $responsibilities->where('name', 'like', '%' . $name . '%');
        }

        if ($role_id) {
            $responsibilities->where('role_id', $role_id);
        }

        return ResponseFormatter::success(
            $responsibilities->paginate($limit),
            'Data list responsibility berhasil diambil'
        );
    }

    public function destroy($id)
    {
        try {
            //Get responsibility
            $responsibility = responsibility::find($id);

            //TODO: Check if responsibility is owned by user
            

            //Check responsibility
            if (!$responsibility) {
                throw new Exception('Data responsibility tidak ada');
            }

            //Delete responsibility
            $responsibility->delete();

            //Return Response
            return ResponseFormatter::success(
                $responsibility,
                'Data responsibility berhasil dihapus'
            );
            //Catch Error
        } catch (Exception $error) {
            return ResponseFormatter::error(
                $error->getMessage(),
                500
            );
        }
    }
}
