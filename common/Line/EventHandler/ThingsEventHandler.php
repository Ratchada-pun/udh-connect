<?php

/**
 * Copyright 2019 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

namespace common\Line\EventHandler;

use LINE\LINEBot;
use LINE\LINEBot\Event\ThingsEvent;
use common\Line\EventHandler;

class ThingsEventHandler implements EventHandler
{
    /** @var LINEBot $bot */
    private $bot;
    /** @var \Monolog\Logger $logger */
    private $logger;
    /** @var ThingsEvent $thingsEvent */
    private $thingsEvent;

    /**
     * ThingsEventHandler constructor.
     *
     * @param LINEBot $bot
     * @param \Monolog\Logger $logger
     * @param ThingsEvent $thingsEvent
     */
    public function __construct($bot, $logger, ThingsEvent $thingsEvent)
    {
        $this->bot = $bot;
        $this->logger = $logger;
        $this->thingsEvent = $thingsEvent;
    }

    /**
     * @throws \ReflectionException
     */
    public function handle()
    {
        $text = 'Device ' . $this->thingsEvent->getDeviceId();
        switch ($this->thingsEvent->getThingsEventType()) {
            case ThingsEvent::TYPE_DEVICE_LINKED:
                $text .= ' was linked!';
                break;
            case ThingsEvent::TYPE_DEVICE_UNLINKED:
                $text .= ' was unlinked!';
                break;
            case ThingsEvent::TYPE_SCENARIO_RESULT:
                $result = $this->thingsEvent->getScenarioResult();
                $text .= ' executed scenario:' . $result->getScenarioId();
                break;
        }
        $this->bot->replyText($this->thingsEvent->getReplyToken(), $text);
    }
}
