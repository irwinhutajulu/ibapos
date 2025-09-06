<?php
namespace App\Http\Controllers\Api;
use App\Models\Supplier;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
class SupplierCustomerController extends Controller
{
    public function suppliers(Request $request)
    {
        $q = $request->input('q');
        $data = Supplier::query()
            ->where('name', 'like', "%$q%")
            ->orderBy('name')
            ->limit(20)
            ->get(['id','name','phone','address']);
        return response()->json($data);
    }
    public function customers(Request $request)
    {
        $q = $request->input('q');
        $data = Customer::query()
            ->where('name', 'like', "%$q%")
            ->orderBy('name')
            ->limit(20)
            ->get(['id','name','phone','address']);
        return response()->json($data);
    }
}
