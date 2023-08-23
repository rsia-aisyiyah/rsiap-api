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

        return "insert into $table values(|$values))";
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

        return "delete from $table where $stringClause";
    }

    public function updateSql($table, $values, $clause)
    {
        $table        = $table->getTable();
        $val          = implode('|', $values);
        $keys         = implode('=?,', array_keys($values));
        $stringClause = $this->stringClause($clause);

        return "update $table set $keys=? where $stringClause |$val ";
    }

    public function create($sql, $nik)
    {
        $data = [
            'tanggal' => date('Y-m-d H:i:s'),
            'sqle'    => $sql,
            'usere'   => $nik,
        ];

        try {
            $result = $this->tracker->create($data);
        } catch (\Illuminate\Database\QueryException $e) {
            $result = $e->errorInfo;
        }

        return $result;
    }
}