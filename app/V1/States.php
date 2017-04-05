<?php

namespace V1;

use JsonData;

/**
 * Description of State
 *
 * @author Ezra Obiwale <contact@ezraobiwale.com>
 */
class States extends JsonData {

    public static function get($node, $path = null, $limit = null, $start = 0) {
        $resp = parent::get($node, $path, $limit, $start);
        // fetching all states
        if (!$path) {
            // show only the number of lgas available
            $resp = array_map(function($value) {
                $value['lgas'] = count($value['lgas']);
                return $value;
            }, $resp);
        }
        // showing something specifig
        else {
            // remove last /
            if ($path[strlen($path) - 1] === '/')
                $path = substr($path, 0, strlen($path) - 1);
            // target a state
            if (!strstr($path, '/')) {
                // also show only the number of lgas the state has
                $resp['lgas'] = count($resp['lgas']);
            }
        }
        return $resp;
    }

}
