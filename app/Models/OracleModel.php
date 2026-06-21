<?php

namespace App\Models;

use Yajra\Oci8\Eloquent\OracleEloquent;

abstract class OracleModel extends OracleEloquent
{
    protected $connection = 'oracle';

    protected $dateFormat = 'Y-m-d H:i:s';
}
