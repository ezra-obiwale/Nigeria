<?php

/**
 *
 * @author Ezra Obiwale <contact@ezraobiwale.com>
 */
interface Data {

    /**
     * Gets the target node from the given path
     * @param string $path
     * @return boolean
     */
    public static function getNodeFromPath(&$path);

    /**
     * Gets the name of the class to call
     * @param string $path
     * @return string
     */
    public static function getTargetClassName(&$path);

    /**
     * Creates the given data at the given node and id, if given
     * @param string $node
     * @param mixed $data
     * @param mixed $id
     * @return mixed The created data
     */
    public static function create($node, $data, $id = null);

    /**
     * Fetches the data at the given node and id
     * @param string $node
     * @param mixed $id
     * @param integer $limit
     * @param integer $start
     * @return array
     */
    public static function get($node, $id = null, $limit = null, $start = 0);

    /**
     * Searches the given node for the given query
     * @param string $node
     * @param string $query
     * @param integer $limit
     * @param integer $start
     */
    public static function search($node, $query = null, $limit = null, $start = 0);

    /**
     * Updates the given node at the given id with the given data
     * @param string $node
     * @param mixed $id
     * @param mixed $data
     * @return mixed The updated data
     */
    public static function update($node, $id, $data);

    /**
     * Deletes data from a given node and id, if given.
     * @param string $node
     * @param mixed $id
     * @return null
     */
    public static function delete($node, $id = null);

    /**
     * Sends the response out
     * @param mixed $response
     * @return void
     */
    public static function output($response);
}
