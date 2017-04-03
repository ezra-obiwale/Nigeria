<?php

/**
 * Description of JsonData
 *
 * @author Ezra Obiwale <contact@ezraobiwale.com>
 */
class JsonData implements Data {

    public static function getNodeFromPath(&$path) {
        if ($node = strstr($path, '/', true)) {
            $path = substr($path, strlen($node) + 1);
            return $node;
        }
        return;
    }

    /**
     * 
     * @param string $node
     * @param mixed $data
     * @param string $path
     * @return array|null
     */
    public static function create($node, $data, $path = null) {
        // creating a new document|object entirely
        if (!$path) {
            // ensure object has id
            $data['id'] = @$data['id'] ? : self::createGUID();
            // use id as the path
            $path = $data['id'];
        }
        // save data to the path on the node
        $updatedNodeData = self::setDataAtPath($data, $path, self::getNode($node));
        // save updated node data to file
        self::saveDataToNode($updatedNodeData, $node);
        // return given data
        return $data;
    }

    /**
     * Fetches the data on the node at the given path
     * @param string $node
     * @param string $path
     * @param integer $limit
     * @param integer $start
     * @return mixed
     */
    public static function get($node, $path = null, $limit = null, $start = 0) {
        // get data at node
        $data = self::getNode($node);
        // get data at id
        return self::getDataAtPath($data, $path);
    }

    /**
     * Searches the given node for the given query
     * @param string $node
     * @param string $query
     * @param integer $limit
     * @param integer $start
     * @return mixed
     */
    public static function search($node, $query = null, $limit = null, $start = 0) {
        
    }

    /**
     * Updates the data on the node at the given path
     * @param string $node
     * @param string $path
     * @param mixed $data
     * @return mixed
     */
    public static function update($node, $path, $data) {
        $newData = self::setDataAtPath($data, $path, self::getNode($node), false);
        self::saveDataToNode($newData, $node);
        return $data;
    }

    /**
     * Deletes data at path on node
     * @param string $node
     * @param string $path
     * @return null
     */
    public static function delete($node, $path = null) {
        // delete path on node
        if ($path) {
            // get data at path
            $data = self::getDataAtPath(self::getNode($node), $path, true);
            // get last key from id
            $paths = explode('/', $path);
            $last_key = null;
            while (!$last_key && count($paths)) $last_key = array_pop($paths);
            if ($last_key && array_key_exists($last_key, $data)) {
                // remove data at last key from general data
                unset($data[$last_key]);
                // save general data
                self::saveDataToNode($data, $node);
            }
        }
        // delete node itself
        else {
            // get file path
            $file_path = self::getFilePath($node);
            // delete file
            unlink($file_path);
        }
        return null;
    }

    /**
     * Sends the response out to the screen
     * @param mixed $response
     */
    public static function output($response) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit(0);
    }

    /**
     * Fetches the data at the given node
     * @param string $node
     * @return array
     */
    private static function getNode($node) {
        if (FALSE === $file_path = self::getFilePath($node)) return null;
        return json_decode(@file_get_contents($file_path), true) ? : [];
    }

    /**
     * Fetches the full path to the node file
     * @param string $node
     * @param boolean $checkExists
     * @return string|boolean
     */
    private static function getFilePath($node, $checkExists = false) {
        $file_path = DATA . $node . '.json';
        return $checkExists && !is_readable($file_path) ? false : $file_path;
    }

    /**
     * Sets the data to the given path on the old data
     * @param mixed $newData
     * @param string $path
     * @param array $oldData
     * @param boolean $overwrite Indicates whether to overwrite or merge with existing data, if any
     * @return array
     */
    private static function setDataAtPath($newData, $path, $oldData, $overwrite = true) {
        $location = & $oldData;
        foreach (explode('/', $path) as $p) {
            if (!$p) continue;
            if (!@$location[$p]) $location[$p] = NULL;
            $location = & $location[$p];
        }
        $location = $overwrite ? $newData : array_merge($location, $newData);
        return $oldData;
    }

    /**
     * Fetches the data at the given path and on the given data
     * @param array $data
     * @param string $path
     * @param boolean $returnParent Indicates whether to return the parent object of the path 
     * instead of the path itself
     * @return mixed
     */
    private static function getDataAtPath($data, $path, $returnParent = false) {
        $paths = explode('/', $path);
        $last_key = array_pop($paths);
        while (!$last_key && count($paths)) $last_key = array_pop($paths);
        foreach ($paths as $p) {
            if (!$p) continue;
            $data = @$data[$p];
            if (!$data) break;
        }
        return !$returnParent && $last_key ? @$data[$last_key] : $data;
    }

    /**
     * Creates a globally unique 36 character id
     */
    public static function createGUID() {
        if (function_exists('com_create_guid')) {
            return substr(com_create_guid(), 1, 36);
        }
        else {
            mt_srand((double) microtime() * 10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);
            $uuid = substr($charid, 0, 8) . $hyphen .
                    substr($charid, 8, 4) . $hyphen .
                    substr($charid, 12, 4) . $hyphen .
                    substr($charid, 16, 4) . $hyphen .
                    substr($charid, 20, 12);

            return $uuid;
        }
    }

    /**
     * Saves the given data to the given node
     * @param array $data
     * @param string $node
     */
    private static function saveDataToNode($data, $node) {
        file_put_contents(self::getFilePath($node), json_encode($data));
    }

}
