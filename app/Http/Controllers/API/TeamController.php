<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;

class TeamController extends Controller
{

    public function create(CreateTeamRequest $request)
    {
        try {
            //Upload icon
            if ($request->file('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }
            //Create Team
            $team = Team::create([
                'name' => $request->name,
                'icon' => $path,
                'company_id' => $request->company_id,
            ]);
            if (!$team) {
                throw new Exception('Data tim gagal ditambahkan');
            }
            return ResponseFormatter::success(
                $team,
                'Data tim berhasil ditambahkan'
            );
        } catch (Exception $error) {
            return ResponseFormatter::error(
                $error->getMessage(),
                500
            );
        }
    }

    public function update(UpdateTeamRequest $request, $id)
    {
        try {
            $team = Team::find($id);

            if (!$team) {
                throw new Exception('Data tim tidak ada');
            }

            //Upload Logo
            if ($request->file('icon')) {
                $path = $request->file('icon')->store('public/icons');
            }

            //Update team
            $team->update([
                'name' => $request->name,
                'icon' => $path,
                'company_id' => $request->company_id,
            ]);

            return ResponseFormatter::success(
                $team,
                'Data tim berhasil diubah'
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

        $teamQuery = Team::query();
        //Get Single team
        if ($id) {
            $team = $teamQuery->find($id);

            if ($team) {
                return ResponseFormatter::success(
                    $team,
                    'Data Tim berhasil diambil'
                );
            }
            return ResponseFormatter::error(
                null,
                'Data Tim tidak ada',
                404
            );
        }

        //Get List team
        $teams = $teamQuery->where('company_id', $request->company_id);

        if ($name) {
            $teams->where('name', 'like', '%' . $name . '%');
        }

        return ResponseFormatter::success(
            $teams->paginate($limit),
            'Data list Tim berhasil diambil'
        );
    }

    public function destroy($id)
    {
        try {
            //Get Team
            $team = Team::find($id);

            //TODO: Check if team is owned by user
            

            //Check Team
            if (!$team) {
                throw new Exception('Data tim tidak ada');
            }

            //Delete Team
            $team->delete();

            //Return Response
            return ResponseFormatter::success(
                $team,
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
