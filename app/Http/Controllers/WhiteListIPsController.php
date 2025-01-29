<?php

namespace App\Http\Controllers;

use App\Models\WhiteListIPs;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class WhiteListIPsController extends Controller
{
    // VIEW 
    public function view()
    {
        return view("whitelistIPs.whitelist_ips");
    }

    // ADD IP ADDRESS
    public function addIPs(Request $request)
    {
        $authId = auth()->user();
        $serverTime = now();

        $validator = $request->validate([
            'ip_name' => 'required|string|max:255|unique:whitelist_ips,name',
            'ip_address' => 'required|ip|unique:whitelist_ips',
        ]);

        $ip_name = $request->input("ip_name");
        $ip_address = $request->input("ip_address");

        $ip = WhiteListIPs::create([
            "name" => $ip_name,
            "ip_address" => $ip_address,
        ]);

        // Log the addition of a new IP address
        Log::channel('ip_restriction')->info("Added By: User ID: {$authId->id} , Username:{$authId->username} , Employee ID: {$authId->employee_id}");
        Log::channel('ip_restriction')->info("Added new IP address: {$ip_address}, Name: {$ip_name}");
        Log::channel('ip_restriction')->info("Timestamp: {$serverTime}\n");

        return response()->json([
            'success' => true,
            'message' => 'IP Address added successfully!',
            'data' => $ip,
        ]);
    }

    // GET IPs POPULATE DATATABLES
    public function getIPs(Request $request)
    {
        // Get the search term from DataTables request
        $search = $request->get('search')['value'];

        // Get the pagination parameters
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);

        // Build the query to fetch the IP addresses
        $ipsQuery = WhitelistIPs::select('id', 'name', 'ip_address');

        // Apply search filter if a search term is provided
        if ($search) {
            $ipsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('ip_address', 'like', '%' . $search . '%');
            });
        }

        // Get the total number of records (without any filtering)
        $totalRecords = WhitelistIPs::count();

        // Get the filtered number of records
        $filteredRecords = $ipsQuery->count();  // Apply filter to count

        // Fetch the actual paginated and filtered data
        $ips = $ipsQuery->skip($start)->take($length)->get();

        // Format the data for DataTables
        $data = $ips->map(function ($ip, $index) {
            return [
                'index' => $index + 1, // DataTables uses 1-based indexing
                'name' => $ip->name,
                'ip' => $ip->ip_address,
                'action' => '
                    <div class="text-center">
                        <button class="btn btn-info edit-btn" data-id="' . $ip->id . '"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-danger delete-btn" data-id="' . $ip->id . '"><i class="fa fa-trash"></i></button>
                    </div>
                ', // Add edit and delete buttons for each row
            ];
        });

        // Return data in DataTable format
        return response()->json([
            'draw' => $request->get('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,  // Make sure to set this to the filtered count
            'data' => $data,  // Return the filtered data
        ]);
    }

    // EDIT IPS (GETTING DATA)
    public function editIPs(Request $request)
    {
        $edit_ip = WhitelistIPs::find($request->id);
        if ($edit_ip) {
            return response()->json([
                "success" => true,
                "data" => $edit_ip,
            ]);
        }
    }

    // UPDATE IPS (FORWARDING DATA)
    public function updateIPs(Request $request)
    {
        $authId = auth()->user();
        $serverTime = now();

        $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip|unique:whitelist_ips,ip_address,' . $request->id,
        ]);

        $update_ip = WhitelistIPs::find($request->id);

        if ($update_ip) {

            $old_name = $update_ip->name;
            $old_ip = $update_ip->ip_address;

            $update_ip->name = $request->name;
            $update_ip->ip_address = $request->ip_address;
            $update_ip->save();


            // Log the update action
            Log::channel('ip_restriction')->info("Updated By: User ID: {$authId->id} , Username:{$authId->username} , Employee ID: {$authId->employee_id}");
            Log::channel('ip_restriction')->info("Old DATA: Name: {$old_name} - IP Address: {$old_ip}");
            Log::channel('ip_restriction')->info("New DATA: Name: {$request->name} - IP Address: {$request->ip_address}");
            Log::channel('ip_restriction')->info("Timestamp: {$serverTime}\n");


            return response()->json([
                'success' => true,
                'message' => 'IP Address updated successfully!',
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'IP Address not found.',
        ]);
    }
    // DELETE IPs
    public function deleteIPs(Request $request)
    {
        $authId = auth()->user();
        $serverTime = now();
        // Get the IP ID from the request
        $ipId = $request->input('ip_id');

        // Find the IP entry by ID
        $ip = WhiteListIPs::find($ipId);

        if ($ip) {
            $deletedIp = $ip->ip_address;
            $deleteName = $ip->name;
            // If the IP entry is found, delete it
            $ip->delete();

            // Log the deletion action
            Log::channel('ip_restriction')->info("Deleted By: User ID: {$authId->id} , Username:{$authId->username} , Employee ID: {$authId->employee_id}");
            Log::channel('ip_restriction')->info("Deleted IP address: {$deletedIp} - Name: {$deleteName}");
            Log::channel('ip_restriction')->info("IP Deleted Successfully - Timestamp: {$serverTime}\n");

            // Return a success response
            return response()->json(['success' => true]);
        }

        // If the IP entry is not found, return an error response
        return response()->json(['success' => false, 'message' => 'IP address not found.'], 404);
    }
}