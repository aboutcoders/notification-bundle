<?php
/*
* This file is part of the notification-bundle package.
*
* (c) Hannes Schulz <hannes.schulz@aboutcoders.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Abc\Bundle\NotificationBundle\Tests\Integration;

use Abc\Bundle\NotificationBundle\Backend\IteratorAwareMessageManagerBackend;
use Abc\Bundle\NotificationBundle\Iterator\ControlledMessageIterator;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Hannes Schulz <hannes.schulz@aboutcoders.com>
 */
class ServiceConfigurationTest extends KernelTestCase
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->container   = static::$kernel->getContainer();
        $this->application = new Application(static::$kernel);
        $this->application->setAutoExit(false);
        $this->application->setCatchExceptions(false);
    }

    /**
     * @param string $service
     * @param string $type
     * @dataProvider getServices
     */
    public function testGetFromContainer($service, $type)
    {
        $subject = $this->container->get($service);

        $this->assertInstanceOf($type, $subject);
    }

    public function testRegistersControlledMessageIterator()
    {
        /**
         * @var IteratorAwareMessageManagerBackend $backend
         */
        $backend = $this->container->get('sonata.notification.backend.doctrine.default');
        $this->assertInstanceOf(ControlledMessageIterator::class, $backend->getIterator());
    }

    /**
     * @return array
     */
    public function getServices()
    {
        return [
            ['sonata.notification.backend.doctrine.default', IteratorAwareMessageManagerBackend::class]
        ];
    }
}