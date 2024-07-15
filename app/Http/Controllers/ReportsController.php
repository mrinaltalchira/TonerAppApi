<?php

namespace App\Http\Controllers;

use App\Models\SupplyChain;
use Illuminate\Http\Request;
use App\Http\Controllers\ReportMail;

class ReportsController extends Controller
{
    public function clientReport(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'client_id' => 'required|integer',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
        ]);

        // Fetch the clients based on the date range
        // Fetch the clients based on the date range
        $report = SupplyChain::selectRaw('
    COUNT(*) as report_count, 
    COALESCE(SUM(CASE WHEN dispatch_receive = 0 THEN 1 ELSE 0 END), 0) as dispatch_count, 
    COALESCE(SUM(CASE WHEN dispatch_receive = 1 THEN 1 ELSE 0 END), 0) as receive_count
')
            ->where('client_id', $request->client_id)
            ->whereBetween('created_at', [$request->from_date, $request->to_date])
            ->get();

 
        // Accessing the counts
        $report_count = $report[0]->report_count;
        $dispatch_count = $report[0]->dispatch_count;
        $receive_count = $report[0]->receive_count;

        // Return the data
        return response()->json([
            'error' => false,
            'message' => 'Success',
            'status' => 200,
            'data' => [
                'message' => 'Reports fatched successfully',
                'report' => $report
            ],
        ]);
    }

    public function sendReport(Request $request)
    {
        // Validate the request
        $request->validate([
            'client_id' => 'required|integer',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'email' => 'required|email',
        ]);

        // Fetch the report data
        $report = SupplyChain::selectRaw('
            COUNT(*) as report_count, 
            COALESCE(SUM(CASE WHEN dispatch_receive = 0 THEN 1 ELSE 0 END), 0) as dispatch_count, 
            COALESCE(SUM(CASE WHEN dispatch_receive = 1 THEN 1 ELSE 0 END), 0) as receive_count
        ')
        ->where('client_id', $request->client_id)
        ->whereBetween('created_at', [$request->from_date, $request->to_date])
        ->get();

        // Send the email

        // Mail::to($request->email)->send(new ReportMail($report));

        // Return a response
        return response()->json(['message' => 'Report sent successfully']);
    }
 
    public function getDashboardDetails()
    {
        try {
            $dashboardDetails = SupplyChain::dashBoardDetails();
            
            return response()->json([
                'success' => true,
                'message' => 'Dashboard details fetched successfully.',
                'data' => $dashboardDetails,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    
}
