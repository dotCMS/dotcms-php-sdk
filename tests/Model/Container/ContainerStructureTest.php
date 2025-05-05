<?php

declare(strict_types=1);

namespace Dotcms\PhpSdk\Tests\Model\Container;

use Dotcms\PhpSdk\Model\Container\ContainerStructure;
use PHPUnit\Framework\TestCase;

class ContainerStructureTest extends TestCase
{
    public function testConstructorAndProperties(): void
    {
        $containerStructure = new ContainerStructure(
            id: 'structure-123',
            structureId: 'struct-456',
            containerInode: 'inode-789',
            containerId: 'container-101',
            code: 'test-container',
            contentTypeVar: 'content-type-var'
        );

        $this->assertEquals('structure-123', $containerStructure->id);
        $this->assertEquals('struct-456', $containerStructure->structureId);
        $this->assertEquals('inode-789', $containerStructure->containerInode);
        $this->assertEquals('container-101', $containerStructure->containerId);
        $this->assertEquals('test-container', $containerStructure->code);
        $this->assertEquals('content-type-var', $containerStructure->contentTypeVar);
    }
}
