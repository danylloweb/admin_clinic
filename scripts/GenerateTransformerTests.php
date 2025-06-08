<?php

class TransformerTestGenerator
{
    private string $baseTestPath = 'tests/Unit/Transformers/';
    private string $transformerPath = 'app/Transformers/';

    private function generateTestClass(string $className, string $testClassName, string $entityClass): string
    {
        $template = <<<PHP
<?php

namespace Tests\Unit\Transformers;

use Tests\TestCase;
use App\Transformers\\{$className};
use App\Entities\\{$entityClass};
use Mockery;

class {$testClassName} extends TestCase
{
    protected \$transformer;

    protected function setUp(): void
    {
        parent::setUp();
        \$this->transformer = new {$className}();
    }

    public function test_transform()
    {
        \$model = Mockery::mock({$entityClass}::class);
        // Configure o mock para retornar valores de teste
        \$model->shouldReceive('getAttribute')->andReturnUsing(function (\$attribute) {
            return match (\$attribute) {
                'id' => 1,
                'created_at', 'updated_at' => now(),
                default => 'test_value',
            };
        });

        \$result = \$this->transformer->transform(\$model);

        // \$this->assertIsArray(\$result);
        // \$this->assertArrayHasKey('id', \$result);
        // \$this->assertEquals(1, \$result['id']);
        // Adicione mais asserções conforme necessário para validar o formato de saída
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

    public function getTransformerClasses(): array
    {
        $transformers = [];
        $files = glob($this->transformerPath . '*Transformer.php');
        
        foreach ($files as $file) {
            $className = basename($file, '.php');
            $transformers[] = 'App\\Transformers\\' . $className;
        }
        
        return $transformers;
    }

    public function generate(string $transformerClass): string
    {
        // Verifica se a classe existe
        if (!class_exists($transformerClass)) {
            throw new Exception("Class {$transformerClass} not found");
        }

        $className = basename(str_replace('\\', '/', $transformerClass));
        $testClassName = $className . 'Test';
        $entityClass = str_replace('Transformer', '', $className);

        $template = $this->generateTestClass($className, $testClassName, $entityClass);
        
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
    $generator = new TransformerTestGenerator();
    $transformers = $generator->getTransformerClasses();

    echo "Found " . count($transformers) . " transformers:\n";
    foreach ($transformers as $transformer) {
        echo "- {$transformer}\n";
    }
    echo "\nGenerating tests...\n";

    foreach ($transformers as $transformer) {
        try {
            $filePath = $generator->generate($transformer);
            echo "✓ Generated test file: {$filePath}\n";
        } catch (Exception $e) {
            echo "✗ Error generating test for {$transformer}: {$e->getMessage()}\n";
        }
    }
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}\n";
} 