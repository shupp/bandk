<?php

class Model_Images
{
    const TYPE_BOOBS = 1;
    const TYPE_KITTENS = 2;

    const CLASS_BOOBS = 'boobs';
    const CLASS_KITTENS = 'kittens';

    protected $_cache = null;
    protected $_db = null;
    protected $_session = null;
    protected $_mogile = null;

    public function __construct()
    {
        $registry = Zend_Registry::getInstance();
        $this->_cache = $registry->cache;
        $this->_db = $registry->db;
        $this->_session = $registry->session;
        $this->_mogile = $registry->mogile;
    }

    public function getBoobsImage()
    {
        return $this->getUniqueImage(self::TYPE_BOOBS);
    }

    public function getKittensImage()
    {
        return $this->getUniqueImage(self::TYPE_KITTENS);
    }

    public function getUniqueImage($type)
    {
        if ($type == self::TYPE_BOOBS) {
            $function = 'getAllBoobsIDs';
            $seen     = 'boobsSeen';
        } else {
            $function = 'getAllKittensIDs';
            $seen     = 'kittensSeen';
        }

        $all  = $this->{$function}();
        if (!isset($this->_session->{$seen})) {
            $this->_session->{$seen} = array();
        }

        $diff = array_diff($all, $this->_session->{$seen});
        if (!count($diff)) {
            // Seen them all - reset!
            $diff = $all;
            $this->_session->{$seen} = array();
        }

        shuffle($diff);
        $current = array_shift($diff);
        $this->_session->{$seen}[] = $current;
        return $current;
    }

    public function getAllBoobsIDs()
    {
        return $this->getAllIDs(self::TYPE_BOOBS);
    }

    public function getAllKittensIDs()
    {
        return $this->getAllIDs(self::TYPE_KITTENS);
    }

    protected function getAllIDs($type)
    {
        $type = ($type == self::TYPE_BOOBS)
                ? self::TYPE_BOOBS : self::TYPE_KITTENS;
        $key  = ($type == self::TYPE_BOOBS) ? 'allboobs' : 'allkittens';

        $ids = null;
        $result = $this->_cache->load($key);
        if ($result) {
            $ids = unserialize($this->_cache->load($key));
        }
        if ($ids === null) {
            $query   = "SELECT * from Images WHERE type=" . $type . ";";
            $results = $this->_db->fetchAll($query);
            $ids   = array();
            foreach ($results as $item) {
                $ids[] = $item['id'];
            }
            $this->_cache->save(serialize($ids), $key);
        }

        return $ids;
    }

    public function getImage($key)
    {
        $cacheKey = 'mogile-' . $key;
        $cacheKey = str_replace('-', '_', $cacheKey);
        $data = $this->_cache->load($cacheKey);
        if (!$data) {
            ob_start();
            $this->_mogile->passthru($key);
            $data = ob_get_contents();
            ob_end_clean();

            $this->_cache->save($data, $cacheKey);
        }
        return $data;
    }

    public function addImage($type, $filename)
    {
        $type  = ($type == self::TYPE_BOOBS)
                 ? self::TYPE_BOOBS : self::TYPE_KITTENS;
        $class = ($type == self::TYPE_BOOBS)
                 ? self::CLASS_BOOBS: self::CLASS_KITTENS;

        if (!is_readable($filename)) {
            throw new Exception('File is not readable');
        }

        try {
            $this->_db->insert('Images', array('type' => $type));
            $id = $this->_db->lastInsertId();
        } catch (Exception $e) {
            echo $e->__toString();
        }

        $mogileKey = $type . '-' . $id;
        $this->_mogile->storeFile($mogileKey, $class, $filename);
    }

    public function deleteImage($type, $id)
    {
        $type  = ($type == self::TYPE_BOOBS)
                 ? self::TYPE_BOOBS : self::TYPE_KITTENS;
        $class = ($type == self::TYPE_BOOBS)
                 ? self::CLASS_BOOBS: self::CLASS_KITTENS;

        $mogileKey = $type . '-' . $id;
        $this->_mogile->delete($mogileKey);
        $this->_db->delete('Images', array('type' => $type, 'id' => $id));
        $this->_cache->remove('all' . $class);
    }
}
