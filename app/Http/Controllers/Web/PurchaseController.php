<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\ShoppingList;
use App\Services\Purchase\PurchaseService;
use App\Services\ShoppingList\ShoppingListService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(
        private PurchaseService $service,
        private ShoppingListService $shoppingListService,
    ) {}

    public function indexPage(Request $request)
    {
        $purchases = $this->service->getAllPurchases($request->household()->id);

        return view('purchase.index', compact('purchases'));
    }

    public function editPage(Request $request, int $purchaseId)
    {
        $purchase = $this->service->getPurchaseById($request->household()->id, $purchaseId);

        return view('purchase.edit', compact('purchase'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['nullable', 'string', 'min:3', 'max:255'],
            'shopping_list_id' => ['sometimes', 'integer'],
        ]);

        $purchase = $this->service->createPurchase(
            $request->name,
            $request->user(),
            $request->shopping_list_id ?? null,
        );

        return redirect()
            ->route('purchases.index')
            ->with('status', 'Compra criada com sucesso');
    }

    public function storeItem(Request $request, Purchase $purchase)
    {
        $data = $request->validate([
            'name' => ['required','string', 'min:3', 'max:255'],
            'price' => ['required', 'min:0.01', 'max:999999.99'],
            'quantity' => ['required', 'min:0.01', 'max:999.999'],
            'unit' => ['required', 'string', 'max:30'],
            'shopping_list_item_id' => ['sometimes', 'nullable', 'integer'],
        ]);

        $this->service->addPurchaseItems(
            $purchase,
            [
                [
                    'name' => $data['name'],
                    'price' => $data['price'],
                    'quantity' => $data['quantity'],
                    'unit' => $data['unit'],
                ],
            ],
            $request->user(),
            $request->shopping_list_item_id ?? null,
        );

        return redirect()
            ->back();
    }

    public function syncItems(Request $request, Purchase $purchase, ShoppingList $shoppingList)
    {
        try {
            $data = $request->validate([
                'items' => ['sometimes', 'array'],
                'items.*' => ['int'],
                // 'items.*.name' => ['required','string', 'min:3', 'max:255'],
                // 'items.*.price' => ['required', 'min:0.01', 'max:999999.99'],
                // 'items.*.quantity' => ['required', 'min:0.01', 'max:999.999'],
                // 'items.*.unit' => ['required', 'string', 'max:30'],
            ]);

            $this->service->syncItems(
                $purchase,
                $request->user(),
                $shoppingList,
                $request?->items ?? null,
            );
        } catch (\Exception $e) {
            logger()->error('deu erro');
            logger()->error($e->getMessage());
        } finally {
            return redirect()
                ->back();
        }
    }

    public function destroyItem(Request $request, Purchase $purchase, int $itemId): RedirectResponse
    {
        try {
            $this->service->deleteItem($purchase, $request->user(), $itemId);

            return redirect()
                ->back()
                ->with('status', 'Item da compra removido');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withErrors('message', $e->getMessage());
        }
    }
}
