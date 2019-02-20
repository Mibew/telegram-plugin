<?php
/*
 * This file is a part of Mibew Telegram Plugin.
 *
 * Copyright 2017-2019 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Mibew\Mibew\Plugin\Telegram;

use Mibew\EventDispatcher\EventDispatcher;
use Mibew\EventDispatcher\Events;
use Symfony\Component\HttpFoundation\Request;

class Plugin extends \Mibew\Plugin\AbstractPlugin implements \Mibew\Plugin\PluginInterface
{

    protected $initialized = false;

    private $bot;

    private $rcpts = array();

    public function __construct($config)
    {
        parent::__construct($config);

        // Use autoloader for Composer's packages that shipped with the plugin
        require(__DIR__ . '/vendor/autoload.php');

        if (isset($config['token'])) {

            // Setup Telegram bot
            $this->bot = new \TelegramBot\Api\BotApi($config['token']);

            $this->initialized = true;

            // Store chat IDs to notify as an array
            if (isset($config['chat'])) {
                if (is_array($config['chat'])) {
                    $this->rcpts = $config['chat'];
                }
                else {
                    $this->rcpts[] = $config['chat'];
                }
            }
        }
    }

    /**
     * This creates the listener that listens for new
     * threads to send out notifications
     */
    public function run()
    {
        $dispatcher = EventDispatcher::getInstance();
        $dispatcher->attachListener(Events::THREAD_CREATE, $this, 'sendTelegramNotification');
    }

    /**
     * Sends notification to Telegram
     * @return boolean
     */
    public function sendTelegramNotification(&$args)
    {
        try {
            foreach ($this->rcpts as $rcpt) {
                $this->bot->sendMessage($rcpt, getlocal('You have a new user waiting for response. Username: {0}', array($args['thread']->userName)));
            }
        } catch (\TelegramBot\Api\HttpException $e) {
            error_log('Exception while sending notification to Telegram: ' . $e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * Returns plugin's version.
     *
     * @return string
     */
    public static function getVersion()
    {
        return '0.0.2';
    }

    /**
     * Returns plugin's dependencies.
     *
     * @return type
     */
    public static function getDependencies()
    {
        return array();
    }
}
