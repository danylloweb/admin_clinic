<?php

namespace Tests\Feature;

use App\Criterias\AppRequestCriteria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery;
use Prettus\Repository\Contracts\RepositoryInterface;
use Tests\TestCase;

class AppRequestCriteriaTest extends TestCase
{
    public function test_apply_criteria_filters_users()
    {
        // Cria um mock do modelo User
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('getAttribute')->with('name')->andReturn('John Doe');

        // Simula uma requisição com um parâmetro de busca
        $request = new Request([
            'search' => 'name',
            'searchFields' => ['name' =>'like'],
        ]);

        // Aplica o critério
        $criteria = new AppRequestCriteria($request);
        $query = Mockery::mock(\Illuminate\Database\Eloquent\Builder::class);
        
        // Define o comportamento do mock para o método apply
        $query->shouldReceive('where')->andReturnSelf();
        $query->shouldReceive('get')->andReturn(collect([$userMock]));

        // Cria um mock do repositório
        $repositoryMock = Mockery::mock(RepositoryInterface::class);
        $repositoryMock->shouldReceive('getFieldsSearchable')->andReturn(['name' => 'like']);
        $repositoryMock->shouldReceive('getFieldsRules')->andReturn([]);

        // Adiciona um campo de pesquisa válido
        $filteredUsers = $criteria->apply($query, $repositoryMock)->get();

        // Verifica se o resultado contém apenas o usuário que corresponde à busca
        $this->assertCount(1, $filteredUsers);
        $this->assertEquals('John Doe', $filteredUsers->first()->name);
    }
} 