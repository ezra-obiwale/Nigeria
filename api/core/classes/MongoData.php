<?php

/**
 * Description of MongoData
 *
 * @author Ezra Obiwale <contact@ezraobiwale.com>
 */
class MongoData extends JsonData {

    /**
     * The error that occured during the operation
     * @var string
     */
    protected static $error;

    /**
     *
     * @var MongoDB
     */
    private static $db;

    /**
     * Creates the data on the given node
     * @param string $node
     * @param array $data
     * @param string $id If not given, it is generated.
     * @return array|null
     */
    public static function create($node, $data, $id = null) {
        // update existing document with descendant document at path
        if ($id && stristr($id, '/', true)) return static::update($node, $id, $data);
        // no id
        $data['_id'] = new MongoId();
        $id = (string) $data['_id'];
        $col = self::selectNode($node);
        if (is_string($error = static::preSave($node, $data, $id, true))) {
            self::$error = $error;
            return false;
        }
        if ($col->insert($data)) {
            static::postSave($node, $data, $id, true);
            return $data;
        }
        return false;
    }

    /**
     * 
     * @param type $node
     * @param type $id
     * @param type $limit
     * @param type $start
     * @return MongoCursor
     */
    public static function get($node, $id = null, $limit = null, $start = 0) {
        $col = self::selectNode($node);
        if (!$id || is_array($id)) {
            $cursor = $col->find($id ? : []);
            if ($limit) $cursor = $cursor->limit($limit);
            if ($start) $cursor = $cursor->skip($start - 1);
            if (static::sortBy()) $cursor = $cursor->sort(static::sortBy());
            return iterator_to_array($cursor);
        }
        else {
            $id_parts = explode('/', $id);
            $id = array_shift($id_parts);
            $result = $col->findOne([
                '_id' => new MongoId($id)
            ]);
            foreach ($id_parts as $part) {
                if (!$result = $result[$part]) break;
            }
            return $result;
        }
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
        if (!$query) return null;
        $col = self::selectNode($node);
        $search = static::searchableKeys(new MongoRegex('/' . $query . '/i'));
        if (!count($search)) return [];
        $cursor = $col->find(['$or' => $search]);
        if ($limit !== null) $cursor->limit($limit);
        if ($start) $cursor->skip($start - 1);
        return iterator_to_array($cursor);
    }

    /**
     * Updates a document/key
     * @param string $node
     * @param string|array $id If array, this is the criteria for the documents to update
     * @param mixed $data
     * @return boolean
     */
    public static function update($node, $id, $data) {
        $col = self::selectNode($node);
        // allow update descendant keys
        if (!is_array($id) && stristr($id, '/', true)) {
            $paths = explode('/', $id);
            $id = array_shift($paths);
            $path = join('.', $paths);
            $data = [$path => $data];
        }
        if (is_string($error = static::preSave($node, $data, $id))) {
            self::$error = $error;
            return false;
        }
        if ($data = $col->findAndModify(is_array($id) ? $id : ['_id' => new MongoId($id)]
                , ['$set' => $data], null, ['new' => true])) {
            static::postSave($node, $data, $id);
            return $data;
        }
        return false;
    }

    /**
     * Deletes a document/key
     * @param string $node
     * @param string|array $id If array, this is the criteria for the documents to delete
     * @return boolean}array
     */
    public static function delete($node, $id = null) {
        $col = self::selectNode($node);
        // allow update descendant keys
        if (is_string($id) && stristr($id, '/', true)) {
            $paths = explode('/', $id);
            $id = array_shift($paths);
            $last_key = array_pop($paths);
            $data = static::get($node, $id);
            $_data = $data;
            foreach ($paths as $key => $path) {
                if (!$key) $_data = &$data[$path];
                else $_data = &$_data[$path];
            }
            $value = $_data[$last_key];
            unset($_data[$last_key]);
            if (static::update($node, $id, $data)) return $value;
        }
        else if ($resp = $col->findAndModify(is_array($id) ? $id : ['_id' => new MongoId($id)]
                , null, null, ['remove' => true])) return $resp;
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function output($response) {
        if ($response['data'] === false) {
            unset($response['data']);
            $response['message'] = static::$error ? : 'Operation failed!';
        }
        parent::output($response);
    }

    /**
     * Selects the collection (node)
     * @param string $node
     * @return MongoCollection
     */
    final protected static function selectNode($node) {
        return self::db()->selectCollection(static::parseNodeName($node));
    }

    /**
     * Creates the connection to the database
     * @param string $dbName If not provided, the default from the config file is used.
     * @return MongoDB
     */
    final protected static function db($dbName = null) {
        $mongo = new MongoClient();
        if (!self::$db)
                self::$db = $mongo->selectDB($dbName ? :
                            config('global', 'mongo', 'db'));
        return self::$db;
    }

    /**
     * Fetches a list of keys that search can be operated on
     * return array
     */
    protected static function searchableKeys($query) {
        return [];
    }

    /**
     * Parses the node name received from request to format to use in thee database
     * @param string $node
     * @return string
     */
    protected static function parseNodeName($node) {
        return $node;
    }

    /**
     * Called before create and update are called
     * @param string $node
     * @param mixed $data
     * @param string $id
     * @param boolean $new
     * @return string|null If string is returned, it is taken as an error message
     */
    protected static function preSave($node, &$data, $id, $new = false) {
        
    }

    /**
     * Called after create and update are called
     * @param string $node
     * @param mixed $data
     * @param string $id
     * @param boolean $new
     */
    protected static function postSave($node, &$data, $id, $new = false) {
        
    }

    /**
     * 
     * @param array $data Array of data to validate: field_name keys to values
     * @param array $rules Array of field_name keys to field rules values. Rules should be separated
     * by pipes (|)
     * @param array $messages Array of field_name keys to field error message values
     * @return string|null String of error message
     */
    protected static function validate(array $data, array $rules, array $messages = []) {
        try {
            return (new Validator($data, $rules, $messages))->run();
        }
        catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * Determines how documents should be sorted when fetching multiple
     * @return array Array of field keys and 1 (asc) or -1 (desc) as values. Multiple fields 
     * sorting is allowed
     */
    protected static function sortBy() {
        
    }

}
