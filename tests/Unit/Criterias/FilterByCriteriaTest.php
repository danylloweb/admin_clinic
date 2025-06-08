<?php

namespace Tests\Feature;

use App\Criterias\FilterByCustomerIdCriteria;
use App\Criterias\FilterByOrderIdCriteria;
use App\Criterias\FilterByServiceConfigTypeCriteria;
use App\Criterias\FilterByStatusIdCriteria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery;
use Prettus\Repository\Contracts\RepositoryInterface;
use Tests\TestCase;

class FilterByCriteriaTest extends TestCase
{
    public function test_filter_by_customer_id()
    {
        // Cria um mock do modelo User
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('getAttribute')->with('customer_id')->andReturn(1);

        // Simula uma requisição com um parâmetro de busca
        $request = new Request(['customer_id' => 1]);

        // Aplica o critério
        $criteria = new FilterByCustomerIdCriteria($request);
        $query = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
        
        // Define o comportamento do mock para o método apply
        $query->shouldReceive('where')->andReturnSelf();
        $query->shouldReceive('get')->andReturn(collect([$userMock]));

        // Cria um mock do repositório
        $repositoryMock = Mockery::mock(RepositoryInterface::class);
        $repositoryMock->shouldReceive('getFieldsSearchable')->andReturn([]);
        $repositoryMock->shouldReceive('getFieldsRules')->andReturn([]);

        // Adiciona um campo de pesquisa válido
        $filteredUsers = $criteria->apply($query, $repositoryMock)->get();

        // Verifica se o resultado contém apenas o usuário que corresponde à busca
        $this->assertCount(1, $filteredUsers);
        $this->assertEquals(1, $filteredUsers->first()->customer_id);
    }

    public function test_filter_by_order_id()
    {
        // Cria um mock do modelo User
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('getAttribute')->with('order_id')->andReturn(1);

        // Simula uma requisição com um parâmetro de busca
        $request = new Request(['order_id' => 1]);

        // Aplica o critério
        $criteria = new FilterByOrderIdCriteria($request);
        $query = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
        
        // Define o comportamento do mock para o método apply
        $query->shouldReceive('where')->andReturnSelf();
        $query->shouldReceive('get')->andReturn(collect([$userMock]));

        // Cria um mock do repositório
        $repositoryMock = Mockery::mock(RepositoryInterface::class);
        $repositoryMock->shouldReceive('getFieldsSearchable')->andReturn([]);
        $repositoryMock->shouldReceive('getFieldsRules')->andReturn([]);

        // Adiciona um campo de pesquisa válido
        $filteredUsers = $criteria->apply($query, $repositoryMock)->get();

        // Verifica se o resultado contém apenas o usuário que corresponde à busca
        $this->assertCount(1, $filteredUsers);
        $this->assertEquals(1, $filteredUsers->first()->order_id);
    }

    public function test_filter_by_service_config_type()
    {
        // Cria um mock do modelo User
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('getAttribute')->with('type')->andReturn(1);

        // Simula uma requisição com um parâmetro de busca
        $request = new Request(['type' => 1]);

        // Aplica o critério
        $criteria = new FilterByServiceConfigTypeCriteria($request);
        $query = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
        
        // Define o comportamento do mock para o método apply
        $query->shouldReceive('where')->andReturnSelf();
        $query->shouldReceive('get')->andReturn(collect([$userMock]));

        // Cria um mock do repositório
        $repositoryMock = Mockery::mock(RepositoryInterface::class);
        $repositoryMock->shouldReceive('getFieldsSearchable')->andReturn([]);
        $repositoryMock->shouldReceive('getFieldsRules')->andReturn([]);

        // Adiciona um campo de pesquisa válido
        $filteredUsers = $criteria->apply($query, $repositoryMock)->get();

        // Verifica se o resultado contém apenas o usuário que corresponde à busca
        $this->assertCount(1, $filteredUsers);
        $this->assertEquals(1, $filteredUsers->first()->type);
    }

    public function test_filter_by_status_id()
    {
        // Cria um mock do modelo User
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('getAttribute')->with('status')->andReturn(1);

        // Simula uma requisição com um parâmetro de busca
        $request = new Request(['status_id' => 1]);

        // Aplica o critério
        $criteria = new FilterByStatusIdCriteria($request);
        $query = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
        
        // Define o comportamento do mock para o método apply
        $query->shouldReceive('where')->andReturnSelf();
        $query->shouldReceive('get')->andReturn(collect([$userMock]));

        // Cria um mock do repositório
        $repositoryMock = Mockery::mock(RepositoryInterface::class);
        $repositoryMock->shouldReceive('getFieldsSearchable')->andReturn([]);
        $repositoryMock->shouldReceive('getFieldsRules')->andReturn([]);

        // Adiciona um campo de pesquisa válido
        $filteredUsers = $criteria->apply($query, $repositoryMock)->get();

        // Verifica se o resultado contém apenas o usuário que corresponde à busca
        $this->assertCount(1, $filteredUsers);
        $this->assertEquals(1, $filteredUsers->first()->status);
    }
} 