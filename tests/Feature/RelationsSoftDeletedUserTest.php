<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Delivery;
use App\Models\Kasbon;
use App\Models\Location;

class RelationsSoftDeletedUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_user_relation_returns_trashed_user()
    {
        $user = User::factory()->create();
        $purchase = Purchase::factory()->make();
        // set the proper foreign key on the purchase via the relation
        $fk = $purchase->user()->getForeignKeyName();
        $purchase->{$fk} = $user->id;
        $purchase->save();

        $user->delete();
        $purchase->refresh();

        $this->assertNotNull($purchase->user);
        $this->assertTrue($purchase->user->trashed());
    }

    public function test_expense_user_relation_returns_trashed_user()
    {
        $user = User::factory()->create();
        $expense = Expense::factory()->make();
        $fk = $expense->user()->getForeignKeyName();
        $expense->{$fk} = $user->id;
        $expense->save();

        $user->delete();
        $expense->refresh();

        $this->assertNotNull($expense->user);
        $this->assertTrue($expense->user->trashed());
    }

    public function test_delivery_assigned_user_relation_returns_trashed_user()
    {
        $user = User::factory()->create();
        $delivery = Delivery::factory()->make();
        // relation name is assignedUser() on Delivery model
        $fk = $delivery->assignedUser()->getForeignKeyName();
        $delivery->{$fk} = $user->id;
        $delivery->save();

        $user->delete();
        $delivery->refresh();

        $this->assertNotNull($delivery->assignedUser);
        $this->assertTrue($delivery->assignedUser->trashed());
    }

    public function test_kasbon_requester_and_approver_relations_return_trashed_users()
    {
        $requester = User::factory()->create();
        $approver = User::factory()->create();

        $kasbon = Kasbon::factory()->make();
        // set request and approve foreign keys via relations
        $fkReq = $kasbon->requester()->getForeignKeyName();
        $fkApp = $kasbon->approver()->getForeignKeyName();
        $kasbon->{$fkReq} = $requester->id;
        $kasbon->{$fkApp} = $approver->id;
        $kasbon->save();

        $requester->delete();
        $approver->delete();

        $kasbon->refresh();

        $this->assertNotNull($kasbon->requester);
        $this->assertTrue($kasbon->requester->trashed());

        $this->assertNotNull($kasbon->approver);
        $this->assertTrue($kasbon->approver->trashed());
    }

    public function test_location_users_relation_includes_trashed_user()
    {
        $user = User::factory()->create();
        $location = Location::factory()->create();

        // attach the user to the location via the pivot
        $location->users()->attach($user->id);

        $user->delete();
        $location->refresh();

        $users = $location->users()->get();
        $this->assertTrue($users->contains(function ($u) use ($user) {
            return $u->id === $user->id && $u->trashed();
        }));
    }
}
