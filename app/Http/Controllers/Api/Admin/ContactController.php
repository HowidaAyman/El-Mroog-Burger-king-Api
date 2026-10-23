<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    use PaginationTrait;

    public function index(Request $request)
    {
        $query = Contact::query();

        if ($request->has('is_read') && $request->is_read !== '') {
            $query->where('is_read', filter_var($request->is_read, FILTER_VALIDATE_BOOLEAN));
        }

        $contacts = $query->latest()->paginate($this->getPerPage($request));

        return ApiResponse::paginated($contacts, 'Contacts retrieved successfully.');
    }

    public function show($id)
    {
        $contact = Contact::findOrFail($id);

        if (!$contact->is_read) {
            $contact->update(['is_read' => true]);
        }

        return ApiResponse::success($contact, 'Contact retrieved successfully.');
    }

    public function toggleStatus($id)
    {
        $contact = Contact::findOrFail($id);

        $contact->update([
            'is_read' => !$contact->is_read
        ]);

        $message = $contact->is_read 
            ? 'Contact marked as read.' 
            : 'Contact marked as unread.';

        return ApiResponse::success($contact, $message);
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return ApiResponse::success(null, 'Contact deleted successfully.');
    }
}