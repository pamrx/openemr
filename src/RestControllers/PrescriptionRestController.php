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

use Particle\Validator\Validator;
use OpenEMR\RestControllers\RestControllerHelper;

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

    public function create($prescription)
    {
        $prescription['date_added'] = date('Y-m-d');
        $prescription['date_modified'] = date('Y-m-d');
        $prescription['provider_id'] = 1;
        $prescription['drug_id'] = "0";
        $prescription['erx_source'] = "0";
        $prescription['erx_uploaded'] = "0";
        $prescription['active'] = "1";
        $prescription['txDate'] = "0000-00-00";

        $validationResult = $this->validate($prescription);
        $validationHandlerResult = RestControllerHelper::validationHandler($validationResult);
        if (is_array($validationHandlerResult)) {
            return $validationHandlerResult;
        }

        $query = "INSERT INTO prescriptions (patient_id, start_date, drug, form, dosage, quantity, size, unit, route, `interval`, refills, per_refill, date_added, date_modified, provider_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $result = sqlInsert(
            $query,
            array(
                $prescription['patient_id'],
                $prescription['start_date'],
                $prescription['drug'],
                $prescription['form'],
                $prescription['dosage'],
                $prescription['quantity'],
                $prescription['size'],
                $prescription['unit'],
                $prescription['route'],
                $prescription['interval'],
                $prescription['refills'],
                $prescription['per_refill'],
                $prescription['date_added'],
                $prescription['date_modified'],
                $prescription['provider_id']
            )
        );

        return RestControllerHelper::responseHandler($result, array('id' => $result), 201);
    }

    private function validate($prescription)
    {
        $validator = new Validator();

        $validator->optional('filled_by_id')->string(); // number
        $validator->optional('pharmacy_id')->string(); // number
        $validator->optional('substitute')->string(); // number 0-1
        $validator->optional('filled_date')->datetime('Y-m-d');
        $validator->optional('medication')->string(); // number 0-1

        $validator->required('patient_id')->string(); // number
        $validator->required('start_date')->datetime('Y-m-d');
        $validator->required('drug')->string();
        $validator->required('form')->string();
        $validator->required('dosage')->string(); // number
        $validator->required('quantity')->string(); // number
        $validator->required('size')->string(); // number
        $validator->required('unit')->string(); // number
        $validator->required('route')->string(); // number
        $validator->required('interval');
        $validator->required('refills')->string(); // number
        $validator->required('per_refill')->string(); // number


        return $validator->validate($prescription);
    }
}
