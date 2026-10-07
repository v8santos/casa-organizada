<?php

namespace App\Http\Controllers\Web;

use App\Concerns\HasHousehold;
use App\Enums\ItemStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\ShoppingList;
use App\Services\ShoppingList\ShoppingListService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShoppingListController extends Controller
{
    public function __construct(private ShoppingListService $service) {}

    public function indexPage(Request $request): View
    {
        $lists = $this->service->getAllShoppingLists($request->household()->id);

        return view('shopping_list.index', compact('lists'));
    }

    public function createPage(): View
    {
        return view('shopping_list.create');
    }

    public function editPage(Request $request, int $listId): View
    {
        $list = $this->service->getShoppingListById($listId, $request->household()->id);

        abort_if($list === null, 404);

        $clipboardContent = $this->service->exportAsPlainText($list);

        return view('shopping_list.edit', compact('list', 'clipboardContent'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $data['owner_id'] = $request->user()->id;
        $data['household_id'] = $request->household()->id;
        $list = $this->service->createShoppingList($data);

        return redirect()
            ->route('shopping-lists.edit', $list)
            ->with('status', 'Lista criada com sucesso.');
    }

    public function toggleStatus(int $listId): RedirectResponse
    {
        $list = ShoppingList::findOrFail($listId);
        $list->update([
            'purchased_at' => $list->purchased_at ? null : now(),
        ]);

        return redirect()
            ->route('shopping-lists.edit', $list)
            ->with('status', 'Status da lista alterado.');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            // TODO: [fix] Isso aqui deveria vir nos params de rota
            'list_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $list = $this->service->getShoppingListById($data['list_id'], $request->user()->id);
        abort_if($list === null, 404);

        $this->service->updateShoppingList($list, $data);

        return redirect()
            ->route('shopping-lists.index')
            ->with('status', 'Lista atualizada com sucesso.');
    }

    public function storeItem(Request $request, int $listId): RedirectResponse
    {
        $list = $this->service->getShoppingListById($listId, $request->household()->id);
        abort_if($list === null || $list->purchased_at, 404);

        $request->merge([
            'item_quantity' => str_replace(',', '.', (string) $request->input('item_quantity')),
        ]);

        $data = $request->validate([
            'item_name' => ['required', 'string', 'max:255'],
            'item_quantity' => ['required', 'numeric', 'min:0.001', 'max:9999999.999'],
            'item_unit' => ['required', 'string', 'max:30'],
            'item_estimated_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
        ], [
            'item_name.required' => 'Informe o nome do item.',
            'item_quantity.required' => 'Informe a quantidade.',
            'item_quantity.numeric' => 'A quantidade deve ser um número válido.',
            'item_quantity.min' => 'A quantidade deve ser maior que zero.',
            'item_unit.required' => 'Selecione a unidade.',
            'item_estimated_price.numeric' => 'Informe um preço válido.',
        ]);

        $this->service->addItemToShoppingList($list, $data);

        return redirect()
            ->route('shopping-lists.edit', $list)
            ->with('status', 'Item adicionado à lista.');
    }

    public function updateItem(Request $request, int $listId, int $itemId): RedirectResponse
    {
        $list = $this->service->getShoppingListById($listId, $request->user()->id);
        abort_if($list === null, 404);

        $request->merge([
            'edit_item_quantity' => str_replace(',', '.', (string) $request->input('edit_item_quantity')),
        ]);

        $data = $request->validate([
            'editing_item_id' => ['required', 'integer', 'in:'.$itemId],
            'edit_item_name' => ['required', 'string', 'max:255'],
            'edit_item_quantity' => ['required', 'numeric', 'min:0.001', 'max:9999999.999'],
            'edit_item_unit' => ['required', 'string', 'max:30'],
            'edit_item_estimated_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
        ], [
            'edit_item_name.required' => 'Informe o nome do item.',
            'edit_item_quantity.required' => 'Informe a quantidade.',
            'edit_item_quantity.numeric' => 'A quantidade deve ser um número válido.',
            'edit_item_quantity.min' => 'A quantidade deve ser maior que zero.',
            'edit_item_unit.required' => 'Selecione a unidade.',
            'edit_item_estimated_price.numeric' => 'Informe um preço válido.',
        ]);

        $this->service->updateShoppingListItem($list, $itemId, $data);

        return redirect()
            ->route('shopping-lists.edit', $list)
            ->with('status', 'Item atualizado com sucesso.');
    }

    public function buy(Request $request, int $listId, int $itemId): RedirectResponse
    {
        $list = ShoppingList::findOrFail($listId);

        $purchased = $list->items()
            ->where('id', $itemId)
            ->update([
                'status' => ItemStatusEnum::PURCHASED,
            ]);

        if (! $purchased) {
            return redirect()
                ->route('shopping-lists.edit', $list)
                ->with('error', 'Falha ao comprar item!');
        }

        return redirect()
            ->route('shopping-lists.edit', $list)
            ->with('status', 'Item comprado com sucesso.');
    }

    public function return(Request $request, int $listId, int $itemId): RedirectResponse
    {
        $list = ShoppingList::findOrFail($listId);

        $purchased = $list->items()
            ->where('id', $itemId)
            ->update([
                'status' => ItemStatusEnum::PENDING,
            ]);

        if (! $purchased) {
            return redirect()
                ->route('shopping-lists.edit', $list)
                ->with('error', 'Falha ao devolver item!');
        }

        return redirect()
            ->route('shopping-lists.edit', $list)
            ->with('status', 'Item devolvido com sucesso.');
    }

    public function shareWhatsApp(Request $request, int $listId): RedirectResponse
    {
        $list = $this->service->getShoppingListById($listId, $request->user()->id);
        abort_if($list === null, 404);

        $message = $this->service->exportAsWhatsAppMessage($list);

        return redirect()->away('https://wa.me/?text='.rawurlencode($message));
    }

    public function destroyItem(Request $request, int $listId, int $itemId): RedirectResponse
    {
        $list = $this->service->getShoppingListById($listId, $request->user()->id);
        abort_if($list === null, 404);

        $this->service->removeItemFromShoppingList($list, $itemId);

        return redirect()
            ->route('shopping-lists.edit', $list)
            ->with('status', 'Item removido da lista.');
    }
}
