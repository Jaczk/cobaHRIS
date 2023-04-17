<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;

class RoleController extends Controller
{

    public function create(CreateRoleRequest $request)
    {
        try {
            //Create role
            $role = role::create([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);
            if (!$role) {
                throw new Exception('Data Role gagal ditambahkan');
            }
            return ResponseFormatter::success(
                $role,
                'Data Role berhasil ditambahkan'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error(
                $error->getMessage(),
                500
            );
        }
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            $role = role::find($id);

            if (!$role) {
                throw new Exception('Data Role tidak ada');
            }

            //Update role
            $role->update([
                'name' => $request->name,
                'company_id' => $request->company_id,
            ]);

            return ResponseFormatter::success(
                $role,
                'Data Role berhasil diubah'
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
        $with_responsibilities = $request->input('with_responsibilities', false);

        $roleQuery = role::query();
        //Get Single role
        if ($id) {
            $role = $roleQuery->with('responsibilities')->find($id);

            if ($role) {
                return ResponseFormatter::success(
                    $role,
                    'Data Role berhasil diambil'
                );
            }
            return ResponseFormatter::error(
                null,
                'Data Role tidak ada',
                404
            );
        }

        //Get List role
        $roles = $roleQuery->where('company_id', $request->company_id);

        if ($name) {
            $roles->where('name', 'like', '%' . $name . '%');
        }

        if ($with_responsibilities) {
            $roles->with('responsibilities');
        }

        return ResponseFormatter::success(
            $roles->paginate($limit),
            'Data list Role berhasil diambil'
        );
    }

    public function destroy($id)
    {
        try {
            //Get role
            $role = role::find($id);

            //TODO: Check if role is owned by user
            

            //Check role
            if (!$role) {
                throw new Exception('Data Role tidak ada');
            }

            //Delete role
            $role->delete();

            //Return Response
            return ResponseFormatter::success(
                $role,
                'Data Role berhasil dihapus'
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
