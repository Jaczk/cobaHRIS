<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;

class EmployeeController extends Controller
{

    public function fetch(Request $request)
    {
        try {
            $id = $request->input('id');
            $name = $request->input('name');
            $email = $request->input('email');
            $age = $request->input('age');
            $phone = $request->input('phone');
            $team_id = $request->input('team_id');
            $role_id = $request->input('role_id');
            $limit = $request->input('limit', 10);

            $employeeQuery = Employee::query();
            //Get Single Employee
            if ($id) {
                $employee = $employeeQuery->with(['team', 'role'])->find($id);

                if ($employee) {
                    return ResponseFormatter::success(
                        $employee,
                        'Data Karyawan berhasil diambil'
                    );
                }
                return ResponseFormatter::error(
                    null,
                    'Data Karyawan tidak ada',
                    404
                );
            }

            //Get List Employee
            $employees = $employeeQuery;

            if ($name) {
                $employees->where('name', 'like', '%' . $name . '%');
            }

            if ($email) {
                $employees->where('email', $email);
            }

            if ($age) {
                $employees->where('age', $age);
            }

            if ($phone) {
                $employees->where('phone', 'like', '%' . $phone . '%');
            }

            if ($team_id) {
                $employees->where('team_id', $team_id);
            }

            if ($role_id) {
                $employees->where('role_id', $role_id);
            }

            return ResponseFormatter::success(
                $employees->paginate($limit),
                'Data list Karyawan berhasil diambil'
            );
        } catch (Exception $e) {
            return ResponseFormatter::error(
                $e->getMessage(),
                500
            );
        }
    }

    public function create(CreateEmployeeRequest $request)
    {
        try {
            //Upload photo
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('public/photos');
            }
            //Create Employee
            $employee = Employee::create([
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'age' => $request->age,
                'phone' => $request->phone,
                'photo' => $path,
                'team_id' => $request->team_id,
                'role_id' => $request->role_id,
            ]);
            if (!$employee) {
                throw new Exception('Data tim gagal ditambahkan');
            }
            return ResponseFormatter::success(
                $employee,
                'Data tim berhasil ditambahkan'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error(
                $error->getMessage(),
                500
            );
        }
    }

    public function update(UpdateEmployeeRequest $request, $id)
    {
        try {
            $employee = Employee::find($id);

            if (!$employee) {
                throw new Exception('Data tim tidak ada');
            }

            //Upload Logo
            if ($request->file('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            //Update Employee
            $employee->update([
                'name' => $request->name,
                'email' => $request->email,
                'gender' => $request->gender,
                'age' => $request->age,
                'phone' => $request->phone,
                'photo' => isset($path) ? $path : $employee->photo ?? null,
                'team_id' => $request->team_id,
                'role_id' => $request->role_id,
            ]);

            return ResponseFormatter::success(
                $employee,
                'Data tim berhasil diubah'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error(
                $error->getMessage(),
                500
            );
        }
    }

    public function destroy($id)
    {
        try {
            //Get Employee
            $Employee = Employee::find($id);

            //TODO: Check if Employee is owned by user


            //Check Employee
            if (!$Employee) {
                throw new Exception('Data tim tidak ada');
            }

            //Delete Employee
            $Employee->delete();

            //Return Response
            return ResponseFormatter::success(
                $Employee,
                'Data tim berhasil dihapus'
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
