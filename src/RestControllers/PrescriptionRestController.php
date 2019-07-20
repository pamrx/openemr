<?php
/**
 * PrescriptionRestController
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 * @author    Matthew Vita <matthewvita48@gmail.com>
 * @copyright Copyright (c) 2018 Matthew Vita <matthewvita48@gmail.com>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */


namespace OpenEMR\RestControllers;

class PrescriptionRestController
{
    public function getAll($pid)
    {
        $sql = "SELECT * FROM prescriptions WHERE patient_id=? ORDER BY start_date DESC";

        $statementResults = sqlStatement($sql, array($pid));

        $results = array();
        while ($row = sqlFetchArray($statementResults)) {
            array_push($results, $row);
        }

        return RestControllerHelper::responseHandler($results, [], 200);
    }
}
