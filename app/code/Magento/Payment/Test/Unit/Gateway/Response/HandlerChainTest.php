<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Payment\Test\Unit\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerChain;

class HandlerChainTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $handler1 = $this->getMockBuilder('Magento\Payment\Gateway\Response\HandlerInterface')
            ->getMockForAbstractClass();
        $handler2 = $this->getMockBuilder('Magento\Payment\Gateway\Response\HandlerInterface')
            ->getMockForAbstractClass();
        $tMapFactory = $this->getMockBuilder('Magento\Framework\ObjectManager\TMapFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $tMap = $this->getMockBuilder('Magento\Framework\ObjectManager\TMap')
            ->disableOriginalConstructor()
            ->getMock();

        $tMapFactory->expects(static::once())
            ->method('create')
            ->with(
                [
                    'array' => [
                        'handler1' => 'Magento\Payment\Gateway\Response\HandlerInterface',
                        'handler2' => 'Magento\Payment\Gateway\Response\HandlerInterface'
                    ],
                    'type' => 'Magento\Payment\Gateway\Response\HandlerInterface'
                ]
            )
            ->willReturn($tMap);
        $tMap->expects(static::once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$handler1, $handler2]));

        $handlingSubject = [];
        $response = [];
        $handler1->expects(static::once())
            ->method('handle')
            ->with($handlingSubject, $response);
        $handler2->expects(static::once())
            ->method('handle')
            ->with($handlingSubject, $response);

        $chain = new HandlerChain(
            [
                'handler1' => 'Magento\Payment\Gateway\Response\HandlerInterface',
                'handler2' => 'Magento\Payment\Gateway\Response\HandlerInterface'
            ],
            $tMapFactory
        );
        $chain->handle($handlingSubject, $response);
    }
}
