<?php

namespace Davichano\DNI;

use Davichano\Services\IPerson;

/**
 *
 */
class Person implements IPerson
{
    /**
     * @var
     *
     * Person identification number, 8 digits
     */
    public $dni;
    /**
     * @var
     *
     * Person name
     */
    private $name;
    /**
     * @var
     *
     * Person lastname
     * Father lastname + " " + Mother lastname
     */
    private $lastname;
    /**
     * @var
     *
     * Verification code provided in the DNI document
     */
    private $code;


    /**
     * @param $dni
     *
     * We need the DNI number to search
     */
    public function __construct($dni)
    {
        $this->dni = $dni;
    }

    /**
     * @return array
     *
     * Serialize method to use in json_encode
     */
    public function jsonSerialize()
    {
        return [
            'dni' => $this->dni,
            'name' => $this->name,
            'lastname' => $this->lastname,
            'code' => $this->code
        ];
    }

    /**
     * @param $apiKeyOCR
     * @return array|false
     *
     * Return the Person data to be used in json_encode or false if exists any error
     * This method use api.ocr.space and for these reason is mandatory send the api key
     */
    public function get_data($apiKeyOCR)
    {
        if (strlen($apiKeyOCR) > 3) {
            $engine = new Engine($apiKeyOCR);
            $data = $engine->search_data($this->dni);
            if ($data) {
                $this->name = trim($data[1]);
                $this->lastname = trim($data[0]);
                $this->code = $this->get_verifyCode();
                return $this->jsonSerialize();
            }
        }
        return false;
    }

    /**
     * @return float|int
     *
     * basic function to calculate the verification code
     */
    private function get_verifyCode()
    {
        $dni = $this->dni;
        $sum = 5;
        $hash = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
        for ($i = 2; $i < 10; ++$i) {
            $sum += ($dni[$i - 2] * $hash[$i]);
        }
        $intNum = (int)($sum / 11);
        $digit = 11 - ($sum - $intNum * 11);
        return $digit > 9 ? $digit - 10 : $digit;
    }
}