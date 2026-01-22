<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\BookingLog;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    // List bookings with pagination
    public function index(Request $request)
    {
        $tenant = Tenant::findOrFail($request->header('X-Tenant-ID'));
        $connection = 'tenant';

        $bookings = (new Booking)->setTenantConnection($connection)->paginate(10);

        return response()->json($bookings);
    }

    // Create booking
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'booking_date' => 'required|date',
        ]);

        // ✅ Get tenant id from header or default tenant
        $tenantId = $request->header('X-Tenant-ID');

        if (!$tenantId) {
            $tenant = \App\Models\Tenant::where('name', 'Default Tenant')->first();
            $tenantId = $tenant->id;
        }

        // ✅ Insert booking with tenant_id
        $booking = \App\Models\Booking::create([
            'tenant_id' => $tenantId,  // <--- must set this
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'booking_date' => $request->booking_date,
        ]);

        // Log action into analytics_db
        DB::connection('analytics')->table('booking_logs')->insert([
            'booking_id' => $booking->id,
            'action' => 'created',
            'changes' => json_encode($booking),
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        return response()->json($booking, 201);
    }

    // Show booking
    public function show(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($request->header('X-Tenant-ID'));
        $connection = 'tenant';

        $booking = (new Booking)->setTenantConnection($connection)->findOrFail($id);
        return response()->json($booking);
    }

    // Update booking
    public function update(BookingRequest $request, $id)
    {
        $tenant = Tenant::findOrFail($request->header('X-Tenant-ID'));
        $connection = 'tenant';

        $booking = (new Booking)->setTenantConnection($connection)->findOrFail($id);
        $booking->update($request->validated());

        BookingLog::create([
            'booking_id' => $booking->id,
            'action' => 'updated',
            'changes' => json_encode($request->validated()),
        ]);

        return response()->json($booking);
    }

    // Cancel booking
    public function destroy(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($request->header('X-Tenant-ID'));
        $connection = 'tenant';

        $booking = (new Booking)->setTenantConnection($connection)->findOrFail($id);
        $booking->update(['status' => 'cancelled']);

        BookingLog::create([
            'booking_id' => $booking->id,
            'action' => 'cancelled',
        ]);

        return response()->json(['message' => 'Booking cancelled successfully']);
    }
}
