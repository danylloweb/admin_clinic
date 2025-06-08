<?php

class TestGenerator
{
    private string $baseTestPath = 'tests/Unit/Repositories/';
    private string $repositoryPath = 'app/Repositories/';
    private array $skipMethods = ['boot', 'getFieldsRules'];

    private function generateTestClass(string $className, string $testClassName, array $methods): string
    {
        // Extrai o nome base do repository (ex: LocationRange de LocationRangeRepositoryEloquent)
        $baseName = str_replace('RepositoryEloquent', '', $className);
        
        $template = <<<PHP
<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\\{$className};
use App\Entities\\{$baseName};
use App\Validators\\{$baseName}Validator;
use App\Presenters\\{$baseName}Presenter;
use Mockery;
use Illuminate\Container\Container;

class {$testClassName} extends TestCase
{
    protected \$repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        \$app = Container::getInstance();
        \$this->repository = new {$className}(\$app);
    }

    public function test_model()
    {
        \$result = \$this->repository->model();
        \$this->assertEquals({$baseName}::class, \$result);
    }

    public function test_validator()
    {
        \$result = \$this->repository->validator();
        \$this->assertEquals({$baseName}Validator::class, \$result);
    }

    public function test_presenter()
    {
        \$result = \$this->repository->presenter();
        \$this->assertEquals({$baseName}Presenter::class, \$result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
PHP;

        return $template;
    }

    public function getRepositoryClasses(): array
    {
        $repositories = [];
        $files = glob($this->repositoryPath . '*RepositoryEloquent.php');
        
        foreach ($files as $file) {
            $className = basename($file, '.php');
            $repositories[] = 'App\\Repositories\\' . $className;
        }
        
        return $repositories;
    }

    public function generate(string $repositoryClass): string
    {
        // Verifica se a classe existe
        if (!class_exists($repositoryClass)) {
            throw new Exception("Class {$repositoryClass} not found");
        }

        $className = basename(str_replace('\\', '/', $repositoryClass));
        $testClassName = $className . 'Test';
        
        $methods = get_class_methods($repositoryClass);
        $methods = array_filter($methods, fn($method) => 
            !in_array($method, $this->skipMethods) && 
            !str_starts_with($method, '__')
        );

        $template = $this->generateTestClass($className, $testClassName, $methods);
        
        $filePath = $this->baseTestPath . $testClassName . '.php';
        if (!is_dir($this->baseTestPath)) {
            mkdir($this->baseTestPath, 0777, true);
        }
        
        file_put_contents($filePath, $template);
        
        return $filePath;
    }
}

// Autoload do Composer
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $generator = new TestGenerator();
    $repositories = $generator->getRepositoryClasses();

    echo "Found " . count($repositories) . " repositories:\n";
    foreach ($repositories as $repository) {
        echo "- {$repository}\n";
    }
    echo "\nGenerating tests...\n";

    foreach ($repositories as $repository) {
        try {
            $filePath = $generator->generate($repository);
            echo "✓ Generated test file: {$filePath}\n";
        } catch (Exception $e) {
            echo "✗ Error generating test for {$repository}: {$e->getMessage()}\n";
        }
    }
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}\n";
} 