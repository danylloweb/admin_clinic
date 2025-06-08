<?php

class PresenterTestGenerator
{
    private string $baseTestPath = 'tests/Unit/Presenters/';
    private string $presenterPath = 'app/Presenters/';

    private function generateTestClass(string $className, string $testClassName): string
    {
        // Remove 'Presenter' suffix for transformer name
        $baseName = str_replace('Presenter', '', $className);
        
        $template = <<<PHP
<?php

namespace Tests\Unit\Presenters;

use Tests\TestCase;
use App\Presenters\\{$className};
use App\Transformers\\{$baseName}Transformer;
use League\Fractal\TransformerAbstract;
use Prettus\Repository\Presenter\FractalPresenter;

class {$testClassName} extends TestCase
{
    protected \$presenter;
    
    protected function setUp(): void
    {
        parent::setUp();
        \$this->presenter = new {$className}();
    }

    public function test_presenter_can_be_instantiated(): void
    {
        \$this->assertInstanceOf({$className}::class, \$this->presenter);
        \$this->assertInstanceOf(FractalPresenter::class, \$this->presenter);
    }

    public function test_get_transformer_returns_correct_transformer(): void
    {
        \$transformer = \$this->presenter->getTransformer();
        
        \$this->assertInstanceOf(TransformerAbstract::class, \$transformer);
        \$this->assertInstanceOf({$baseName}Transformer::class, \$transformer);
    }

    public function test_get_transformer_returns_new_instance(): void
    {
        \$transformer1 = \$this->presenter->getTransformer();
        \$transformer2 = \$this->presenter->getTransformer();
        
        \$this->assertNotSame(\$transformer1, \$transformer2, 'getTransformer should return a new instance each time');
    }
}
PHP;

        return $template;
    }

    public function getPresenterClasses(): array
    {
        $presenters = [];
        $files = glob($this->presenterPath . '*Presenter.php');
        
        foreach ($files as $file) {
            $className = basename($file, '.php');
            $presenters[] = 'App\\Presenters\\' . $className;
        }
        
        return $presenters;
    }

    public function generate(string $presenterClass): string
    {
        // Verify if class exists
        if (!class_exists($presenterClass)) {
            throw new Exception("Class {$presenterClass} not found");
        }

        $className = basename(str_replace('\\', '/', $presenterClass));
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
    $generator = new PresenterTestGenerator();
    $presenters = $generator->getPresenterClasses();

    echo "Found " . count($presenters) . " presenters:\n";
    foreach ($presenters as $presenter) {
        echo "- {$presenter}\n";
    }
    echo "\nGenerating tests...\n";

    foreach ($presenters as $presenter) {
        try {
            $filePath = $generator->generate($presenter);
            echo "✓ Generated test file: {$filePath}\n";
        } catch (Exception $e) {
            echo "✗ Error generating test for {$presenter}: {$e->getMessage()}\n";
        }
    }
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}\n";
} 