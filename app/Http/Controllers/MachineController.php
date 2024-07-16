<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

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


    

    public function updateMachine(Request $request)
    {
        try {
            // Retrieve the machine to update
            $machine = Machine::findOrFail($request->id);
    
            // Validate input
            $request->validate([ 
                'model_code' => [
                    'sometimes',
                    'required',
                    Rule::unique('machines')->ignore($machine->id),
                ],
                // Add other validation rules as needed
            ]);
    
            // Update machine data
            $machine->model_name = $request->input('model_name', $machine->model_name);
            $machine->model_code = $request->input('model_code', $machine->model_code);

            // Save the updated machine
            $machine->save();
    
            return response()->json([
                'error' => false,
                'message' => 'Machine updated successfully',
                'status' => 200,
                'data' => [
                    'machine' => $machine
                ],
            ]);
        } catch (ValidationException $e) {
            // Handle validation exception
            $errors = $e->validator->errors();
        
            // Initialize error messages
            $errorMessage = '';
        
            // Check if specific errors exist for model_name and model_code
            if ($errors->has('model_name') && $errors->has('model_code')) {
                $errorMessage = 'Model Name and Model Code are already taken.';
            } elseif ($errors->has('model_code')) {
                $errorMessage = $errors->first('model_code');
            } else {
                $errorMessage = 'Validation error'; // Fallback if no specific errors found
            }
        
            return response()->json([
                'error' => true,
                'message' => $errorMessage,
                'status' => 422,
                'errors' => $errors,
            ]);
        } catch (QueryException $e) {
            // Handle database query exception (e.g., unique constraint violation)
            return response()->json([
                'error' => true,
                'message' => 'Database error: '. $e->getMessage(),
                'status' => 500,
            ]);
        } catch (\Exception $e) {
            // Catch-all for any other unexpected exceptions
            return response()->json([
                'error' => true,
                'message' => 'Error: '. $e->getMessage(),
                'status' => 500,
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
