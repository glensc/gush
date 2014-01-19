<?php

/*
 * This file is part of Gush.
 *
 * (c) Luis Cordova <cordoval@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Gush\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Console\Input\InputOption;
use Gush\Event\GushEvents;
use Gush\Event\CommandEvent;
use Symfony\Component\Console\Event\ConsoleEvent;
use Gush\Feature\TableFeature;

class TableSubscriber implements EventSubscriberInterface
{
    protected $validLayouts = [
        'default',
        'compact',
        'borderless',
    ];

    public static function getSubscribedEvents()
    {
        return [
            GushEvents::DECORATE_DEFINITION => 'decorateDefinition',
            GushEvents::INITIALIZE => 'initialize',
        ];
    }

    public function decorateDefinition(CommandEvent $event)
    {
        $command = $event->getCommand();

        if ($command instanceof TableFeature) {
            $command->addOption(
                'table-layout', 
                null, 
                InputOption::VALUE_REQUIRED,
                'Specify the layout for the table, one of default, compact or borderless',
                'default'
            );
            $command->addOption(
                'table-no-header', 
                null, 
                InputOption::VALUE_NONE,
                'Disable the header on the table'
            );
            $command->addOption(
                'table-no-footer', 
                null, 
                InputOption::VALUE_NONE,
                'Disable the footer on the table'
            );
        }
    }

    public function initialize(ConsoleEvent $event)
    {
        $command = $event->getCommand();

        if ($command instanceof TableFeature) {
            $input = $event->getInput();
            $layout = $input->getOption('table-layout');

            if ($layout) {
                if (!in_array($layout, $this->validLayouts)) {
                    throw new \InvalidArgumentException(sprintf(
                        'The table-layout option must be passed one of "%s" but was given "%s"',
                        implode(', ', $this->validLayouts),
                        $layout
                    ));
                }
            }
        }
    }
}
