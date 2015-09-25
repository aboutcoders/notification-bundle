<?php

namespace Abc\Bundle\NotificationBundle\Command;

use Abc\ProcessControl\Controller;
use Sonata\NotificationBundle\Backend\BackendInterface;
use Sonata\NotificationBundle\Backend\QueueDispatcherInterface;
use Sonata\NotificationBundle\Command\ConsumerHandlerCommand as BaseCommand;
use Sonata\NotificationBundle\Consumer\ConsumerInterface;
use Sonata\NotificationBundle\Event\IterateEvent;
use Sonata\NotificationBundle\Exception\HandlingException;
use Sonata\NotificationBundle\Model\MessageInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

/**
 * Custom implementation of the command according to the custom iterator wired into the queue backend.
 *
 * The implementation is heavily based on the default provided by sonata, only the way the iterator is used differs.
 *
 * @author Hannes Schulz <schulz@daten-bahn.de>
 */
class ConsumerHandlerCommand extends BaseCommand
{

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $startDate = new \DateTime();

        $output->writeln(sprintf('[%s] <info>Checking listeners</info>', $startDate->format('r')));
        foreach($this->getNotificationDispatcher()->getListeners() as $type => $listeners)
        {
            $output->writeln(sprintf(" - %s", $type));
            foreach($listeners as $listener)
            {
                if(!$listener[0] instanceof ConsumerInterface)
                {
                    throw new \RuntimeException(sprintf('The registered service does not implement the ConsumerInterface (class=%s', get_class($listener[0])));
                }

                $output->writeln(sprintf('   > %s', get_class($listener[0])));
            }
        }

        $type        = $input->getOption('type');
        $showDetails = $input->getOption('show-details');

        $output->write(sprintf('[%s] <info>Retrieving backend</info> ...', $startDate->format('r')));
        $backend = $this->getBackend($type);

        $output->writeln("");
        $output->write(sprintf('[%s] <info>Initialize backend</info> ...', $startDate->format('r')));

        // initialize the backend
        $backend->initialize();

        $output->writeln(" done!");

        if($type === null)
        {
            $output->writeln(sprintf("[%s] <info>Starting the backend handler</info> - %s", $startDate->format('r'), get_class($backend)));
        }
        else
        {
            $output->writeln(sprintf("[%s] <info>Starting the backend handler</info> - %s (type: %s)", $startDate->format('r'), get_class($backend), $type));
        }


        $i                = 0;
        $startMemoryUsage = memory_get_usage(true);

        try
        {
            do
            {
                if($i > 0)
                {
                    usleep(500000);
                }

                $i++;
                $this->iterate($input, $output, $backend, $showDetails, $startMemoryUsage);
            } while(!$this->getProcessController()->doExit() && (!$input->getOption('iteration') || $i < (int)$input->getOption('iteration')));
        }
        catch(\Exception $e)
        {
            $output->writeln(sprintf("<error>KO - %s</error>", $e->getMessage()));
        }

        $output->writeln('End of iteration cycle');
    }

    /**
     * @param InputInterface   $input
     * @param OutputInterface  $output
     * @param BackendInterface $backend
     * @param                  $showDetails
     * @param                  $startMemoryUsage
     */
    protected function iterate(InputInterface $input, OutputInterface $output, BackendInterface $backend, $showDetails, $startMemoryUsage)
    {
        $iterator = $backend->getIterator();
        foreach($iterator as $message)
        {
            if(!$message instanceof MessageInterface)
            {
                throw new \RuntimeException('The iterator must return a MessageInterface instance');
            }

            if(!$message->getType())
            {
                $output->write("<error>Skipping : no type defined </error>");
                continue;
            }

            $date = new \DateTime();
            $output->write(sprintf("[%s] <info>%s</info>", $date->format('r'), $message->getType()));
            $memoryUsage = memory_get_usage(true);

            try
            {
                $start       = microtime(true);
                $returnInfos = $backend->handle($message, $this->getNotificationDispatcher());

                $currentMemory = memory_get_usage(true);

                $output->writeln(
                    sprintf(
                        "<comment>OK! </comment> - %0.04fs, %ss, %s, %s - %s = %s, %0.02f%%",
                        microtime(true) - $start,
                        $date->format('U') - $message->getCreatedAt()->format('U'),
                        $this->formatMemory($currentMemory - $memoryUsage),
                        $this->formatMemory($currentMemory),
                        $this->formatMemory($startMemoryUsage),
                        $this->formatMemory($currentMemory - $startMemoryUsage),
                        ($currentMemory - $startMemoryUsage) / $startMemoryUsage * 100
                    )
                );

                if($showDetails && null !== $returnInfos)
                {
                    $output->writeln($returnInfos->getReturnMessage());
                }
            }
            catch(HandlingException $e)
            {
                $output->writeln(sprintf("<error>KO! - %s</error>", $e->getPrevious()->getMessage()));
            }
            catch(\Exception $e)
            {
                $output->writeln(sprintf("<error>KO! - %s</error>", $e->getMessage()));
            }

            $this->getEventDispatcher()->dispatch(IterateEvent::EVENT_NAME, new IterateEvent($iterator, $backend, $message));
        }
    }

    /**
     * @param $memory
     *
     * @return string
     */
    private function formatMemory($memory)
    {
        if($memory < 1024)
        {
            return $memory . "b";
        }
        elseif($memory < 1048576)
        {
            return round($memory / 1024, 2) . "Kb";
        }

        return round($memory / 1048576, 2) . "Mb";
    }

    /**
     * @param string $type
     *
     * @return \Sonata\NotificationBundle\Backend\BackendInterface
     */
    private function getBackend($type = null)
    {
        $backend = $this->getContainer()->get('sonata.notification.backend');

        if($type && !array_key_exists($type, $this->getNotificationDispatcher()->getListeners()))
        {
            throw new \RuntimeException(
                sprintf("The type `%s` does not exist, available types: %s", $type, implode(", ", array_keys($this->getNotificationDispatcher()->getListeners())))
            );
        }

        if($type !== null && !$backend instanceof QueueDispatcherInterface)
        {
            throw new \RuntimeException(sprintf("Unable to use the provided type %s with a non QueueDispatcherInterface backend", $type));
        }

        if($backend instanceof QueueDispatcherInterface)
        {
            return $backend->getBackend($type);
        }

        return $backend;
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private function getNotificationDispatcher()
    {
        return $this->getContainer()->get('sonata.notification.dispatcher');
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private function getEventDispatcher()
    {
        return $this->getContainer()->get('event_dispatcher');
    }

    /**
     * @return Controller
     */
    public function getProcessController()
    {
        return $this->getContainer()->get('abc.process_control.controller');
    }
}