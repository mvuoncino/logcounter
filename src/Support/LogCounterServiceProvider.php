<?php

namespace MVuoncino\LogCounter\Support;

use App;
use Config;
use Exception;
use Illuminate\Cache\StoreInterface;
use Illuminate\Support\ServiceProvider;
use Log;
use MVuoncino\LogCounter\Models\LogCountHandler;

class LogCounterServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function provides()
    {
        return [
            LogCountHandler::class
        ];
    }

    public function boot()
    {
        $this->package('mvuoncino/logcounter');
        if (Config::get('logcounter::enabled', false)) {
            $logCounter = self::getLogCounterHandler();
            $logCounter->setStore(self::getStore());
            Log::getMonolog()->pushHandler($logCounter);
        }
    }

    public function register()
    {
        $this->app->singleton(
            LogCountHandler::class,
            function ($app, $params) {
                return new LogCountHandler();
            }
        );
    }
    
    /**
     * @return LogCountHandler
     */
    public static function getLogCounterHandler()
    {
        return App::make(LogCountHandler::class);
    }

    /**
     * @return StoreInterface
     */
    public static function getStore()
    {
        try {
            $class = Config::get('logcounter::storage', \Illuminate\Cache\NullStore::class);
            return App::make($class);
        } catch (Exception $e) {
            Log::error('Exception when creating store: ' . $e->getMessage());
        }
    }

}
