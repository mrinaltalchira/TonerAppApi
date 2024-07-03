<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MachineController extends Controller
{
    public function addMachine(Request $request)
    { 
 


        $model_code_exists = Machine::where('model_code', $request->model_code)->exists(); 
        if ($model_code_exists) {

            return response()->json([
                'error' => false,
                'message' => 'model code already exists!',
                'status' => 200,
            ]);

        } else { 
            $machine = Machine::create([
                'model_name' => $request->model_name,
                'model_code' => $request->model_code,
                'add_by' => $request->user->id
            ]);

            $machine->save();

            return response()->json([
                'error' => false,
                'message' => 'Success',
                'status' => 200,
                'data' => [
                    'message' => 'Machine created successfully',
                    'machine' => $machine
                ],
            ]);
        }
    }

    public function allMachine(Request $request){
        try {
            $searchQuery = $request->query('search');
    
            if (!empty($searchQuery)) {
                // Perform search query
                $machine = Machine::where(function($query) use ($searchQuery) {
                    $query->where('model_code', 'like', "%$searchQuery%")
                          ->orWhere('model_name', 'like', "%$searchQuery%");
                })
                ->orderBy('created_at', 'desc')
                ->get();
            } else {
                // Fetch all clients if no search query provided
                $machine = Machine::orderBy('created_at', 'desc')->get();
            }
            
            return response()->json([
                'error' => false,
                'message' => 'Success',
                'status' => 200,
                'data' => [
                    'message' => 'Machine fetched successfully',
                    'machine' => $machine
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Error',
                'status' => 500,
            ]);
        }
    }

    
}
