<?php

namespace Tests\Unit\Services;

use App\Services\AppService;
use Tests\TestCase;
use Mockery;
use Carbon\Carbon;

class AppServiceTest extends TestCase
{
    protected $appService;
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock('Repository');
        $this->appService = new AppService();
        $this->appService->setRepository($this->repository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test CRUD Operations */

    public function test_it_should_return_all_paginated()
    {
        $limit = 20;
        $expectedResult = ['item1', 'item2'];

        $this->repository->shouldReceive('paginate')
            ->once()
            ->with($limit)
            ->andReturn($expectedResult);

        $result = $this->appService->all($limit);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_create_new_record()
    {
        $data = ['name' => 'Test'];
        $expectedResult = ['id' => 1, 'name' => 'Test'];

        $this->repository->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->appService->create($data);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_find_record_by_id()
    {
        $id = 1;
        $expectedResult = ['id' => 1, 'name' => 'Test'];

        $this->repository->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn($expectedResult);

        $result = $this->appService->find($id);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_delete_record()
    {
        $id = 1;
        
        $this->repository->shouldReceive('delete')
            ->once()
            ->with($id)
            ->andReturn(true);

        $result = $this->appService->delete($id);
        $this->assertEquals(['success' => true], $result);
    }

    public function test_it_should_restore_deleted_record()
    {
        $id = 1;
        
        $this->repository->shouldReceive('restore')
            ->once()
            ->with($id)
            ->andReturn(true);

        $result = $this->appService->restore($id);
        $this->assertEquals(['success' => true], $result);
    }

    public function test_it_should_update_existing_record()
    {
        $id = 1;
        $data = ['name' => 'Updated Test'];
        $expectedResult = ['id' => 1, 'name' => 'Updated Test'];

        $this->repository->shouldReceive('update')
            ->once()
            ->with($data, $id)
            ->andReturn($expectedResult);

        $result = $this->appService->update($data, $id);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_find_record_by_id_with_skip_presenter()
    {
        $id = 1;
        $expectedResult = ['id' => 1, 'name' => 'Test'];

        $this->repository->shouldReceive('skipPresenter->find')
            ->once()
            ->with($id)
            ->andReturn($expectedResult);

        $result = $this->appService->find($id, true);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_find_where_with_first()
    {
        $data = ['name' => 'Test'];
        $expectedResult = ['id' => 1, 'name' => 'Test'];

        $this->repository->shouldReceive('skipPresenter->findWhere')
            ->once()
            ->with($data)
            ->andReturn(collect([$expectedResult]));

        $result = $this->appService->findWhere($data, true);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_find_where_with_presenter()
    {
        $data = ['name' => 'Test'];
        $expectedResult = collect(['id' => 1, 'name' => 'Teste']);

        $this->repository->shouldReceive('findWhere')
            ->once()
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->appService->findWhere($data, false, true);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_find_where_without_presenter()
    {
        $data = ['name' => 'Test'];
        $expectedResult = collect(['id' => 1, 'name' => 'Test']);

        $this->repository->shouldReceive('skipPresenter->findWhere')
            ->once()
            ->with($data)
            ->andReturn($expectedResult);

        $result = $this->appService->findWhere($data);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_find_last()
    {
        $data = ['name' => 'Test'];
        $collection = collect([
            ['id' => 1, 'name' => 'Test'],
            ['id' => 2, 'name' => 'Test']
        ]);
        $expectedResult = ['id' => 2, 'name' => 'Test'];

        $this->repository->shouldReceive('skipPresenter->findWhere')
            ->once()
            ->with($data)
            ->andReturn($collection);

        $result = $this->appService->findLast($data);
        $this->assertEquals($expectedResult, $result);
    }

    /** @test Utility Methods */

    public function test_it_should_remove_accentuation()
    {
        $value = "áéíóúâêîôûãõàèìòùäëïöü";
        $expectedResult = "aeiouaeiouaoaeiouaeiou";

        $result = $this->appService->removeAccentuation($value);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @dataProvider dateFormatProvider
     */
    public function test_it_should_format_date_to_db($inputDate, $expectedDate)
    {
        $result = $this->appService->formatDateDB($inputDate);
        $this->assertEquals($expectedDate, $result);
    }

    public static function dateFormatProvider(): array
    {
        return [
            'already formatted date' => ["2024-12-31", "2024-12-31"],
            'american format date' => ["12/31/2024", "2024-12-31"],
        ];
    }

    /**
     * @dataProvider ageCalculationProvider
     */
    public function test_it_should_get_age_by_date_birth($currentDate, $birthDate, $expectedAge)
    {
        Carbon::setTestNow($currentDate);

        $result = $this->appService->getAgeByDateBirth($birthDate);
        
        $this->assertEquals($expectedAge, $result);

        Carbon::setTestNow();
    }

    public static function ageCalculationProvider(): array
    {
        return [
            'basic age calculation' => ['2024-01-15', '1990-01-01', 35],
            'birthday not happened yet' => ['2024-01-01', '1990-12-31', 34],
            'birthday just happened' => ['2024-12-31', '1990-12-31', 34],
        ];
    }

    public function test_it_should_remove_spaces_from_string()
    {
        $value = "test with spaces";
        $expectedResult = "testwithspaces";

        $result = $this->appService->removeSpaces($value);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_clear_special_characters()
    {
        $value = "Test@123 Special#";
        $expectedResult = "Test123Special";

        $result = $this->appService->clearCharacters($value);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_it_should_remove_special_characters()
    {
        $value = "Test@123 Special#";
        $expectedResult = "Test123Special";

        $result = $this->appService->removeSpecialCharacters($value);
        $this->assertEquals($expectedResult, $result);
    }
} 