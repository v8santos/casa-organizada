<?php

namespace Tests\Feature;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShoppingListTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_their_shopping_lists(): void
    {
        $user = User::factory()->create();
        $ownList = ShoppingList::create([
            'name' => 'Feira da semana',
            'owner_id' => $user->id,
        ]);
        $otherList = ShoppingList::create([
            'name' => 'Lista de outra pessoa',
            'owner_id' => User::factory()->create()->id,
        ]);

        $this->actingAs($user)
            ->get(route('shopping-lists.index'))
            ->assertOk()
            ->assertSee($ownList->name)
            ->assertDontSee($otherList->name);
    }

    public function test_user_can_create_a_shopping_list(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('shopping-lists.store'), [
            'name' => 'Compras do mês',
        ]);

        $list = ShoppingList::where('owner_id', $user->id)->firstOrFail();

        $response->assertRedirect(route('shopping-lists.edit', $list));
        $this->assertSame('Compras do mês', $list->name);
    }

    public function test_user_cannot_edit_another_users_list(): void
    {
        $user = User::factory()->create();
        $list = ShoppingList::create([
            'name' => 'Lista privada',
            'owner_id' => User::factory()->create()->id,
        ]);

        $this->actingAs($user)
            ->get(route('shopping-lists.edit', $list))
            ->assertNotFound();
    }

    public function test_user_can_add_an_item_to_their_shopping_list(): void
    {
        $user = User::factory()->create();
        $list = ShoppingList::create([
            'name' => 'Feira',
            'owner_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('shopping-lists.items.store', $list), [
            'item_name' => 'Banana',
            'item_quantity' => 2,
            'item_unit' => 'kg',
            'item_estimated_price' => 6.50,
        ]);

        $response
            ->assertRedirect(route('shopping-lists.edit', $list))
            ->assertSessionHas('status', 'Item adicionado à lista.');

        $this->assertDatabaseHas('shopping_list_items', [
            'shopping_list_id' => $list->id,
            'name' => 'Banana',
            'quantity' => 2,
            'unit' => 'kg',
        ]);
    }

    public function test_user_can_add_an_item_with_a_decimal_quantity_using_comma(): void
    {
        $user = User::factory()->create();
        $list = ShoppingList::create([
            'name' => 'Feira',
            'owner_id' => $user->id,
        ]);

        $this->actingAs($user)->post(route('shopping-lists.items.store', $list), [
            'item_name' => 'Queijo',
            'item_quantity' => '0,5',
            'item_unit' => 'kg',
            'item_estimated_price' => 40,
        ])->assertRedirect(route('shopping-lists.edit', $list));

        $this->assertSame(0.5, $list->items()->firstOrFail()->quantity);
    }

    public function test_user_cannot_add_an_item_to_another_users_list(): void
    {
        $user = User::factory()->create();
        $list = ShoppingList::create([
            'name' => 'Lista privada',
            'owner_id' => User::factory()->create()->id,
        ]);

        $this->actingAs($user)->post(route('shopping-lists.items.store', $list), [
            'item_name' => 'Item indevido',
            'item_quantity' => 1,
            'item_unit' => 'un',
        ])->assertNotFound();

        $this->assertDatabaseMissing('shopping_list_items', [
            'name' => 'Item indevido',
        ]);
    }

    public function test_user_can_update_an_item_from_their_shopping_list(): void
    {
        $user = User::factory()->create();
        $list = ShoppingList::create([
            'name' => 'Feira',
            'owner_id' => $user->id,
        ]);
        $item = $list->items()->create([
            'name' => 'Banana',
            'quantity' => 2,
            'unit' => 'kg',
            'estimated_price' => 6.50,
        ]);

        $response = $this->actingAs($user)->patch(route('shopping-lists.items.update', [$list, $item]), [
            'editing_item_id' => $item->id,
            'edit_item_name' => 'Banana prata',
            'edit_item_quantity' => 3,
            'edit_item_unit' => 'kg',
            'edit_item_estimated_price' => 7.25,
        ]);

        $response
            ->assertRedirect(route('shopping-lists.edit', $list))
            ->assertSessionHas('status', 'Item atualizado com sucesso.');

        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $item->id,
            'name' => 'Banana prata',
            'quantity' => 3,
            'unit' => 'kg',
        ]);
    }

    public function test_user_cannot_update_an_item_from_another_list(): void
    {
        $user = User::factory()->create();
        $list = ShoppingList::create([
            'name' => 'Minha lista',
            'owner_id' => $user->id,
        ]);
        $otherList = ShoppingList::create([
            'name' => 'Outra lista',
            'owner_id' => $user->id,
        ]);
        $item = $otherList->items()->create([
            'name' => 'Item protegido',
            'quantity' => 1,
            'unit' => 'un',
        ]);

        $this->actingAs($user)->patch(route('shopping-lists.items.update', [$list, $item]), [
            'editing_item_id' => $item->id,
            'edit_item_name' => 'Item alterado',
            'edit_item_quantity' => 1,
            'edit_item_unit' => 'un',
        ])->assertNotFound();

        $this->assertSame('Item protegido', ShoppingListItem::findOrFail($item->id)->name);
    }

    public function test_user_can_share_their_list_on_whatsapp(): void
    {
        $user = User::factory()->create();
        $list = ShoppingList::create([
            'name' => 'Compras do mês',
            'owner_id' => $user->id,
        ]);
        $list->items()->create([
            'name' => 'Arroz',
            'quantity' => 2,
            'unit' => 'pct',
        ]);

        $response = $this->actingAs($user)->get(route('shopping-lists.share.whatsapp', $list));

        $response->assertRedirect();
        $location = rawurldecode($response->headers->get('Location'));

        $this->assertStringStartsWith('https://wa.me/?text=', $location);
        $this->assertStringContainsString('*Compras do mês*', $location);
        $this->assertStringContainsString('Arroz — 2 pct', $location);
        $this->assertStringNotContainsString('☐', $location);
    }

    public function test_user_cannot_share_another_users_list_on_whatsapp(): void
    {
        $user = User::factory()->create();
        $list = ShoppingList::create([
            'name' => 'Lista privada',
            'owner_id' => User::factory()->create()->id,
        ]);

        $this->actingAs($user)
            ->get(route('shopping-lists.share.whatsapp', $list))
            ->assertNotFound();
    }

    public function test_user_can_delete_an_item_from_their_shopping_list(): void
    {
        $user = User::factory()->create();
        $list = ShoppingList::create([
            'name' => 'Feira',
            'owner_id' => $user->id,
        ]);
        $item = $list->items()->create([
            'name' => 'Banana',
            'quantity' => 2,
            'unit' => 'kg',
        ]);

        $response = $this->actingAs($user)
            ->delete(route('shopping-lists.items.destroy', [$list, $item]));

        $response
            ->assertRedirect(route('shopping-lists.edit', $list))
            ->assertSessionHas('status', 'Item removido da lista.');

        $this->assertDatabaseMissing('shopping_list_items', [
            'id' => $item->id,
        ]);
    }

    public function test_user_cannot_delete_an_item_from_another_list(): void
    {
        $user = User::factory()->create();
        $list = ShoppingList::create([
            'name' => 'Minha lista',
            'owner_id' => $user->id,
        ]);
        $otherList = ShoppingList::create([
            'name' => 'Outra lista',
            'owner_id' => $user->id,
        ]);
        $item = $otherList->items()->create([
            'name' => 'Item protegido',
            'quantity' => 1,
            'unit' => 'un',
        ]);

        $this->actingAs($user)
            ->delete(route('shopping-lists.items.destroy', [$list, $item]))
            ->assertNotFound();

        $this->assertDatabaseHas('shopping_list_items', [
            'id' => $item->id,
            'name' => 'Item protegido',
        ]);
    }
}
