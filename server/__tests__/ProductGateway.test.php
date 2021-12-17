<?php
    namespace App\Tests;

    use PHPUnit\Framework\TestCase;
    use App\Lib\Database;
    use App\Model\ProductGateway;

    class ProductGatewayTest extends TestCase
    {
        public function testFindAll()
        {
            $dbMock = $this->getMockBuilder(Database::class)->getMock();
            $dbMock->method('findAll')->willReturn(1);
            
            $productGateway = new ProductGateway($dbMock);
            $result = $productGateway->findAll();

            $expectedResult = 1;
            $this->assertEquals($expectedResult, $result);
        }
    }
?>