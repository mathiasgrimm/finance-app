<?php

namespace Tests\Unit;

use App\Transaction;
use App\TransactionImport;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_null_when_not_importing()
    {
        $user = factory(User::class)->create();

        $this->assertNull(TransactionImport::currentlyImporting($user));
    }

    public function test_it_returns_current_when_its_importing()
    {
        $transactionImport = factory(TransactionImport::class)->create();
        $this->assertEquals(
            $transactionImport->id,
            TransactionImport::currentlyImporting($transactionImport->user_id)->id
        );
    }

    public function test_it_return_current_when_multi_users_are_importing()
    {
        $transactionImports = factory(TransactionImport::class, 5)->create();

        $this->assertEquals(
            $transactionImports[0]->id,
            TransactionImport::currentlyImporting($transactionImports[0]->user_id)->id
        );
    }


}
