<?php

namespace MVuoncino\LogCounter\Models;

use Illuminate\Cache\StoreInterface;
use Monolog\Handler\AbstractProcessingHandler;

class LogCountHandler extends AbstractProcessingHandler
{
    const CACHE_FOR_MINUTES = 2880;

    const PREFIX = 'log_count_handler';

    const SUFFIX_COUNT = 'count';

    const SUFFIX_LAST = 'last';

    /**
     * @var StoreInterface
     */
    private $store;

    /**
     * @param StoreInterface $store
     * @return self
     */
    public function setStore(StoreInterface $store)
    {
        $this->store = $store;
        return $this;
    }
    
    /**
     * @param array $record
     */
    protected function write(array $record)
    {
        $countKey = self::getMessageKey($record, self::SUFFIX_COUNT);
        $lastKey = self::getMessageKey($record, self::SUFFIX_LAST);
        $this->store->increment($countKey);
        $this->store->put($lastKey, $record['message'], self::CACHE_FOR_MINUTES);
    }

    /**
     * @param array $record
     * @return string
     */
    protected static function getMessageKey(array $record, $suffix)
    {
        $messageKey = sprintf('%s:%s:%s:%s',
            self::PREFIX,
            $record['datetime']->format('Ymd'),
            soundex($record['message']),
            $suffix
        );
        return $messageKey;
    }
}
