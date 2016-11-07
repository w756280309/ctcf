<?php

namespace Wcg\Lock;

class FileLock
{
    private $key;
    private $expire;
    private $lockFile;

    public function __construct($filePath, $key, $expire = 300)
    {
        $this->key = $key;
        $this->expire = $expire;
        if (empty($filePath) || (!file_exists($filePath) && !mkdir($filePath))) {
            throw new \Exception('文件目录不合法');
        }
        $this->lockFile = rtrim($filePath, '/') . '/' . $this->key . rand(1, 100);;
    }

    /**
     * 加锁
     * @return bool|int
     */
    public function acquire()
    {
        $lockFile = $this->lockFile;
        if (file_exists($lockFile)) {
            $tag = file_get_contents($lockFile);
            if (!empty($tag) && $tag > time()) {
                return false;
            }
        }
        return file_put_contents($lockFile, time() + $this->expire);
    }

    /**
     * 释放锁
     * @return bool
     */
    public function release()
    {
        $lockFile = $this->lockFile;
        if (file_exists($lockFile)) {
            return unlink($lockFile);
        } else {
            return true;
        }
    }
}