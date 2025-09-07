<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->string('q'));
        $products = Product::query()
            ->when($q, fn($b) => $b->where('name', 'like', "%$q%")
                                    ->orWhere('barcode', 'like', "%$q%"))
            ->select('id','name','barcode','price')
            ->orderBy('name')
            ->limit(20)
            ->get();
        return response()->json($products);
    }

    public function search(Request $request)
    {
        $q = trim((string)$request->string('q'));
        $categoryId = $request->get('category_id');
        $trashed = $request->boolean('trashed');
        
        $query = Product::query()
            ->with('category')
            ->when($q, function($builder) use ($q) {
                return $builder->where('name', 'like', "%$q%")
                              ->orWhere('barcode', 'like', "%$q%");
            })
            ->when($categoryId, fn($builder) => $builder->where('category_id', $categoryId))
            ->orderBy('name');

        if ($trashed) {
            $query->withTrashed();
        }

        $products = $query->paginate(15);
        
        // Format data for table component
        $tableRows = $products->getCollection()->map(function($p) {
            return [
                'id' => $p->id,
                'cells' => [
                    [
                        'type' => 'avatar',
                        'image' => $p->image_url ?? asset('images/default-product.svg'),
                        'name' => $p->name,
                        'subtitle' => $p->deleted_at ? 'Deleted' : ($p->barcode ?? 'No Barcode')
                    ],
                    $p->barcode ?? '-',
                    $p->category->name ?? '-',
                    [
                        'type' => 'currency',
                        'value' => $p->price,
                        'formatted' => 'Rp ' . number_format($p->price, 0, ',', '.')
                    ]
                ],
                'deleted_at' => $p->deleted_at,
                'actions' => $this->getProductActions($p)
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $tableRows,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'has_more' => $products->hasMorePages()
            ]
        ]);
    }

    private function getProductActions($p)
    {
        // Check if this is a test route (no auth required)
        $isTestRoute = request()->route() && str_contains(request()->route()->getName() ?? '', 'test.');
        
        if ($isTestRoute) {
            return [
                [
                    'type' => 'link',
                    'url' => '#',
                    'label' => 'View',
                    'style' => 'secondary',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 0 1 6 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>'
                ],
                [
                    'type' => 'link',
                    'url' => '#',
                    'label' => 'Edit',
                    'style' => 'primary',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>'
                ],
                [
                    'type' => 'button',
                    'label' => 'Delete',
                    'style' => 'danger',
                    'onclick' => 'alert("Delete action - Test mode")',
                    'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>'
                ]
            ];
        }
        
        return collect([
            !$p->deleted_at && auth()->user()->can('products.read') ? [
                'type' => 'link',
                'url' => route('products.show', $p),
                'label' => 'View',
                'style' => 'secondary',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 0 1 6 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>'
            ] : null,
            !$p->deleted_at && auth()->user()->can('products.update') ? [
                'type' => 'link',
                'url' => route('products.edit', $p),
                'label' => 'Edit',
                'style' => 'primary',
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>'
            ] : null,
            !$p->deleted_at && auth()->user()->can('products.delete') ? [
                'type' => 'button',
                'label' => 'Delete',
                'style' => 'danger',
                'onclick' => "deleteProduct({$p->id})",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>'
            ] : null,
            $p->deleted_at ? [
                'type' => 'button',
                'label' => 'Restore',
                'style' => 'success',
                'onclick' => "restoreProduct({$p->id})",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>'
            ] : null,
            $p->deleted_at ? [
                'type' => 'button',
                'label' => 'Force Delete',
                'style' => 'danger',
                'onclick' => "forceDeleteProduct({$p->id})",
                'icon' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>'
            ] : null
        ])->filter()->values()->toArray();
    }
}
