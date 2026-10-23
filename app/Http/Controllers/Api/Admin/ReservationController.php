<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    use PaginationTrait;

    public function index(Request $request)
    {
        $query = Reservation::query();

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $reservations = $query->latest('date')->latest('time')->paginate($this->getPerPage($request));

        return ApiResponse::paginated($reservations, 'Reservations retrieved successfully.');
    }

    public function show($id)
    {
        $reservation = Reservation::findOrFail($id);

        return ApiResponse::success($reservation, 'Reservation retrieved successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled'
        ]);

        $reservation = Reservation::findOrFail($id);
        
        $reservation->update([
            'status' => $request->status
        ]);

        return ApiResponse::success($reservation, 'Reservation status updated successfully.');
    }

    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return ApiResponse::success(null, 'Reservation deleted successfully.');
    }
}