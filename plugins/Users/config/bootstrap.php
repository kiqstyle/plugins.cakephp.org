<?php
/*
 * Read configuration file and inject configuration into various
 * CakePHP classes.
 *
 * By default there is only one configuration file. It is often a good
 * idea to create multiple configuration files, and separate the configuration
 * that changes from configuration that does not. This makes deployment simpler.
 */
try {
    Configure::load('Users.app', 'default', true);
} catch (\Exception $e) {
    exit($e->getMessage() . "\n");
}

use ADmad\SocialAuth\Middleware\SocialAuthMiddleware;
use Cake\Core\Configure;
use Cake\Event\EventManager;

EventManager::instance()->on('Server.buildMiddleware', function ($event, $middleware) {
    $config = Configure::read('Users.social');
    if (empty($config['serviceConfig']['provider'])) {
        return;
    }

    if (empty($config['getUserCallback'])) {
        $config['getUserCallback'] = 'getUserFromSocialProfile';
    }

    $userModel = Configure::read('Users.userModel');
    if (empty($userModel)) {
        throw new LogicException('Configure value Users.userModel is empty');
    }
    $config['userModel'] = $userModel;

    $middleware->add(new SocialAuthMiddleware($config));
});
