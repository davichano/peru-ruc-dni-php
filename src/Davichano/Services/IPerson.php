<?php

namespace Davichano\Services;

interface IPerson
{
    public function jsonSerialize();

    public function get_data($apiKeyOCR);
}