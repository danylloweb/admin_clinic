<?php

class ControllerTestGenerator
{
    private string $baseTestPath = 'tests/Unit/Controllers/';
    private string $controllerPath = 'app/Http/Controllers/';
    private array $skipMethods = ['__construct', 'middleware', 'getMiddleware'];

    private function getServiceName(string $className): string
    {
        // Remove 'Controller' suffix
        $baseName = str_replace('Controller', '', $className);
        
        // Try both versions of the service name
        $serviceNameWithS = "App\\Services\\{$baseName}Service";
        $serviceNameWithoutS = "App\\Services\\" . rtrim($baseName, 's') . "Service";
        
        // Check which service class exists
        if (class_exists($serviceNameWithoutS)) {
            return rtrim($baseName, 's') . "Service";
        }
        
        return $baseName . "Service";
    }

    private function getValidatorName(string $className): string
    {
        // Remove 'Controller' suffix
        $baseName = str_replace('Controller', '', $className);
        
        // Try both versions of the validator name
        $validatorNameWithS = "App\\Validators\\{$baseName}Validator";
        $validatorNameWithoutS = "App\\Validators\\" . rtrim($baseName, 's') . "Validator";
        
        // Check which validator class exists
        if (class_exists($validatorNameWithoutS)) {
            return rtrim($baseName, 's') . "Validator";
        }
        
        return $baseName . "Validator";
    }

    private function generateTestClass(string $className, string $testClassName): string
    {
        // Get the correct service name
        $serviceName = $this->getServiceName($className);
        
        // Check if class uses validator
        $reflectionClass = new \ReflectionClass("App\\Http\\Controllers\\{$className}");
        $constructor = $reflectionClass->getConstructor();
        $hasValidator = false;
        
        if ($constructor) {
            $parameters = $constructor->getParameters();
            $hasValidator = count($parameters) > 1 && str_contains($parameters[1]->getType()->getName(), 'Validator');
        }
        
        // Get validator name
        $validatorName = $this->getValidatorName($className);
        $validatorSetup = $hasValidator ? "\$this->validator = Mockery::mock({$validatorName}::class);" : '';
        $validatorProperty = $hasValidator ? "    protected \$validator;\n" : '';
        $validatorParam = $hasValidator ? ", \$this->validator" : '';
        $validatorUse = $hasValidator ? "use App\Validators\\{$validatorName};\n" : '';
        
        $template = <<<PHP
<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use App\Http\Controllers\\{$className};
use App\Services\\{$serviceName};
use Mockery;
use Illuminate\Http\Request;
{$validatorUse}
class {$testClassName} extends TestCase
{
    protected \$controller;
    protected \$service;
{$validatorProperty}
    protected function setUp(): void
    {
        parent::setUp();
        \$this->service = Mockery::mock({$serviceName}::class);
        {$validatorSetup}
        \$this->controller = new {$className}(\$this->service{$validatorParam});
    }

    public function test_controller_can_be_instantiated(): void
    {
        \$this->assertInstanceOf({$className}::class, \$this->controller);
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

    public function getControllerClasses(): array
    {
        $controllers = [];
        $files = glob($this->controllerPath . '*Controller.php');
        
        foreach ($files as $file) {
            $className = basename($file, '.php');
            $controllers[] = 'App\\Http\\Controllers\\' . $className;
        }
        
        return $controllers;
    }

    public function generate(string $controllerClass): string
    {
        // Verify if class exists
        if (!class_exists($controllerClass)) {
            throw new Exception("Class {$controllerClass} not found");
        }

        $className = basename(str_replace('\\', '/', $controllerClass));
        $testClassName = $className . 'Test';
        
        $template = $this->generateTestClass($className, $testClassName);
        
        $filePath = $this->baseTestPath . $testClassName . '.php';
        if (!is_dir($this->baseTestPath)) {
            mkdir($this->baseTestPath, 0777, true);
        }
        
        file_put_contents($filePath, $template);
        
        return $filePath;
    }
}

// Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $generator = new ControllerTestGenerator();
    $controllers = $generator->getControllerClasses();

    echo "Found " . count($controllers) . " controllers:\n";
    foreach ($controllers as $controller) {
        echo "- {$controller}\n";
    }
    echo "\nGenerating tests...\n";

    foreach ($controllers as $controller) {
        try {
            $filePath = $generator->generate($controller);
            echo "✓ Generated test file: {$filePath}\n";
        } catch (Exception $e) {
            echo "✗ Error generating test for {$controller}: {$e->getMessage()}\n";
        }
    }
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}\n";
} 