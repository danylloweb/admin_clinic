<?php

class RequestTestGenerator
{
    private string $baseTestPath = 'tests/Unit/Requests/';
    private string $requestPath = 'app/Http/Requests/';

    private function generateTestClass(string $className, string $testClassName): string
    {
        $template = <<<PHP
<?php

namespace Tests\Unit\Requests;

use Tests\TestCase;
use App\Http\Requests\\{$className};
use Illuminate\Validation\Rule;

class {$testClassName} extends TestCase
{
    protected \$request;
    
    protected function setUp(): void
    {
        parent::setUp();
        \$this->request = new {$className};
    }

    public function test_request_can_be_instantiated(): void
    {
        \$this->assertInstanceOf({$className}::class, \$this->request);
    }

    public function test_request_has_rules_method(): void
    {
        \$rules = \$this->request->rules();
        \$this->assertIsArray(\$rules);
    }

    public function test_request_has_authorize_method(): void
    {
        \$authorize = \$this->request->authorize();
        \$this->assertIsBool(\$authorize);
    }

    public function test_request_has_messages_method(): void
    {
        \$hasMessagesMethod = method_exists(\$this->request, 'messages');
        \$this->assertTrue(\$hasMessagesMethod, 'Request should have messages method');
        
        if (\$hasMessagesMethod) {
            \$messages = \$this->request->messages();
            \$this->assertIsArray(\$messages, 'Messages method should return an array');
        }
    }
}
PHP;

        return $template;
    }

    public function getRequestClasses(): array
    {
        $requests = [];
        $files = glob($this->requestPath . '*Request.php');
        
        foreach ($files as $file) {
            $className = basename($file, '.php');
            $requests[] = 'App\\Http\\Requests\\' . $className;
        }
        
        return $requests;
    }

    public function generate(string $requestClass): string
    {
        // Verify if class exists
        if (!class_exists($requestClass)) {
            throw new Exception("Class {$requestClass} not found");
        }

        $className = basename(str_replace('\\', '/', $requestClass));
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
    $generator = new RequestTestGenerator();
    $requests = $generator->getRequestClasses();

    echo "Found " . count($requests) . " requests:\n";
    foreach ($requests as $request) {
        echo "- {$request}\n";
    }
    echo "\nGenerating tests...\n";

    foreach ($requests as $request) {
        try {
            $filePath = $generator->generate($request);
            echo "✓ Generated test file: {$filePath}\n";
        } catch (Exception $e) {
            echo "✗ Error generating test for {$request}: {$e->getMessage()}\n";
        }
    }
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}\n";
} 