<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model;

use Dotcms\PhpSdk\Model\Core\AbstractModel;
use PHPUnit\Framework\TestCase;

class AbstractModelTest extends TestCase
{
    /**
     * A concrete implementation of AbstractModel for testing
     */
    private function createConcreteModel(array $additionalProperties = []): ConcreteModel
    {
        return new ConcreteModel('test-id', 'Test Title', $additionalProperties);
    }

    public function testConstructorAndBasicProperties(): void
    {
        $model = $this->createConcreteModel();

        $this->assertEquals('test-id', $model->identifier);
        $this->assertEquals('Test Title', $model->title);
    }

    public function testAdditionalProperties(): void
    {
        $additionalProps = [
            'customProp1' => 'value1',
            'customProp2' => 123,
            'customProp3' => ['nested' => 'value'],
        ];

        $model = $this->createConcreteModel($additionalProps);

        // Test accessing via array access
        $this->assertEquals('value1', $model['customProp1']);
        $this->assertEquals(123, $model['customProp2']);
        $this->assertEquals(['nested' => 'value'], $model['customProp3']);

        // Test accessing via jsonSerialize
        $json = $model->jsonSerialize();
        $this->assertEquals('value1', $json['customProp1']);
        $this->assertEquals(123, $json['customProp2']);
        $this->assertEquals(['nested' => 'value'], $json['customProp3']);
    }

    public function testArrayAccessExists(): void
    {
        $model = $this->createConcreteModel(['customProp' => 'value']);

        $this->assertTrue(isset($model['identifier']));
        $this->assertTrue(isset($model['title']));
        $this->assertTrue(isset($model['customProp']));
        $this->assertFalse(isset($model['nonExistentProp']));

        // Test with non-string offset
        $this->assertFalse(isset($model[123]));
    }

    public function testArrayAccessGet(): void
    {
        $model = $this->createConcreteModel(['customProp' => 'value']);

        $this->assertEquals('test-id', $model['identifier']);
        $this->assertEquals('Test Title', $model['title']);
        $this->assertEquals('value', $model['customProp']);
        $this->assertNull($model['nonExistentProp']);

        // Test with non-string offset
        $this->assertNull($model[123]);
    }

    public function testArrayAccessSetThrowsException(): void
    {
        $model = $this->createConcreteModel();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Properties are read-only');

        $model['identifier'] = 'new-value';
    }

    public function testArrayAccessUnsetThrowsException(): void
    {
        $model = $this->createConcreteModel();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Properties are read-only');

        unset($model['identifier']);
    }

    public function testJsonSerialize(): void
    {
        $additionalProps = ['customProp' => 'value'];
        $model = $this->createConcreteModel($additionalProps);

        $expected = [
            'identifier' => 'test-id',
            'title' => 'Test Title',
            'customProp' => 'value',
        ];

        $this->assertEquals($expected, $model->jsonSerialize());

        // Test JSON encoding
        $this->assertEquals(json_encode($expected), json_encode($model));
    }
}

/**
 * Concrete implementation of AbstractModel for testing
 */
class ConcreteModel extends AbstractModel
{
    public function __construct(
        public readonly string $identifier,
        public readonly string $title,
        array $additionalProperties = [],
    ) {
        $this->setAdditionalProperties($additionalProperties);
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            [
                'identifier' => $this->identifier,
                'title' => $this->title,
            ],
            $this->getAdditionalProperties()
        );
    }
}
