<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrackerSqlController extends Controller
{
    protected $tracker;

    public function __construct()
    {
        $this->tracker = new \App\Models\TrackerSql();
    }

    public function insertSql($table, $values)
    {
        $table  = $table->getTable();
        $values = implode('|', $values);

        $str = "insert into $table values(|$values))";

        $this->create($str);
    }

    public function stringClause($clause)
    {
        $count        = 1;
        $stringClause = '';
        foreach ($clause as $cls) {
            if ($count < count($clause)) {
                $and = ' AND ';
                $count++;
            } else {
                $and = '';
            }

            $keyClasue    = array_keys($clause, $cls, true);
            $stringClause .= "$keyClasue[0]" . '=' . "'$cls'" . $and;
        }

        return $stringClause;
    }

    public function deleteSql($table, $clause)
    {
        $table        = $table->getTable();
        $stringClause = $this->stringClause($clause);

        $str = "delete from $table where $stringClause";

        $this->create($str);
    }

    public function updateSql($table, $values, $clause)
    {
        $table        = $table->getTable();
        $val          = implode('|', $values);
        $keys         = implode('=?,', array_keys($values));
        $stringClause = $this->stringClause($clause);

        $str = "update $table set $keys=? where $stringClause |$val ";

        $this->create($str);
    }

    public function create($sql)
    {
        $payload = auth()->payload();
        $data = [
            'tanggal' => date('Y-m-d H:i:s'),
            'sqle'    => $sql,
            'usere'   => $payload->get('sub'),
        ];

        try {
            $result = $this->tracker->create($data);
        } catch (\Illuminate\Database\QueryException $e) {
            $result = $e->errorInfo;
        }

        return $result;
    }
}