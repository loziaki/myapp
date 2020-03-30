<?php
namespace Service\Model;

class QueryBuilder
{
    //最后根据这里的参数去
    private $select = '';
    private $from = '';
    private $where = '';
    private $groupBy = '';
    private $offset = '';
    private $limit = '';
    private $orderBy = '';

    //如果传参错误就放这里
    private $errorFlag = false;
    private $errorMsg = [];

    //传入的参数
    private $queryData = [];

    const PARAM_IS_NULL = '##ISNULL##';
    const PARAM_NOT_NULL = '##NOTNULL##';

    public function __construct()
    {
        return;
    }

    public function build()
    {
        if ($this->errorFlag == true) {
            throw new \Exception(implode(';', $this->errorMsg));
        }

        $sql = 'SELECT '.$this->select.' ';
        $sql.= 'FROM '.$this->from.' ';
        $sql.= 'WHERE '.$this->where;
        $sql.= (empty($this->groupBy))? null : ' '.$this->groupBy;
        $sql.= (empty($this->orderBy))? null : ' '.$this->orderBy;
        $sql.= (empty($this->limit))? ' ' : ' '.$this->limit;

        return [$sql,$this->queryData];
    }

    public function field(...$args)
    {
        if ($args[0] === '*') {
            $this->select = '*';
        } elseif (is_array($args[0]) && count($args[0]) > 0) {
            $this->setFiled($args[0]);
        } elseif (is_string($args[0]) && !empty($args[0])) {
            $param = (isset($args[1]) && is_string($args[1]))? [$args[0],$args[1]] : $args[0];
            $this->setFiled([$param]);
        } else {
            $this->error("'field' only acccept array or not-empty string");
            return;
        }
    }

    private function setFiled($param)
    {
        $this->select = '';
        foreach ($param as $item) {
            if (is_array($item)) {
                $name = $item[0];
                $otherName = ' AS `'.$item[1].'`';
            } elseif (is_string($item)) {
                $name = $item;
                $otherName = null;
            } else {
                $this->error("'field' only acccept string array or not-empty string");
                break;
            }

            $field = (preg_match('/[\+\-\*\/\>\(]/', $name))? $name : '`'.$name.'`';
            $this->select.= $field.$otherName.',';
        }
        $this->select = substr($this->select, 0, -1);
    }


    public function table($param)
    {
        if (is_string($param)) {
            $this->from = $param;
        } else {
            $this->error("'table' only acccept string");
        }
    }

    public function where(...$args)
    {
        if (is_string($args[0])) {
            $string = $this->getFilterString([$args]);
        } elseif (is_array($args[0])) {
            $string = $this->getFilterString($args[0]);
        } else {
            $this->error("'where' only acccept string array or string");
            return;
        }
        $this->mergeWhereString($string);
    }

    public function orWhere(...$args)
    {
        if (is_string($args[0])) {
            $string = $this->getFilterString([$args], true);
        } elseif (is_array($args[0])) {
            $string = $this->getFilterString($args[0], true);
        } else {
            $this->error("'where' only acccept array or string");
            return;
        }
        $this->mergeWhereString($string);
    }

    private function mergeWhereString($string)
    {
        if (!empty($this->where)) {
            $this->where.= 'AND '.$string;
        } else {
            $this->where = $string;
        }
    }

    private function getFilterString($arr, $CONCAT_WITH_OR = false)
    {
        if (empty($arr) || !is_array($arr)) {
            return '';
        }

        $flag = true;
        $whereArr = [];
        foreach ($arr as $item) {
            switch (count($item)) {
                case 2:
                    if (is_null($item[1]) || $item[1] === QueryBuilder::PARAM_IS_NULL) {
                        $whereArr[] = '`'.$item[0].'` is null';
                    } elseif ($item[1] === QueryBuilder::PARAM_NOT_NULL) {
                        $whereArr[] = '`'.$item[0].'` not null';
                    } else {
                        $whereArr[] = '`'.$item[0].'` = ?';
                        $this->queryData[] = $item[1];
                    }
                    break;
                case 3:
                    if (in_array(strtolower($item[1]), ['in','not in'])) {
                        if (is_array($item[2])) {
                            foreach ($item[2] as &$v) {
                                if (is_string($v)) {
                                    str_replace('"', '\"', $v);
                                    $v = '"'.$v.'"';
                                } else {
                                    if (!is_numeric($v)) {
                                        $this->error("Incorrect values is passed for 'in\\not in'");
                                        break;
                                    }
                                }
                            };
                            $whereArr[] = '`'.$item[0].'` '.$item[1].' ('.implode(',', $item[2]).')';
                        } else {
                            $this->error("a array must be passed for 'in\\not in'");
                            break;
                        }
                    } else {
                        $field = (preg_match('/^[a-zA-Z]+\(.+\)$/', $item[0]))? $item[0] : '`'.$item[0].'`';
                        $whereArr[] = $field.' '.$item[1].' ?';
                        $this->queryData[] = $item[2];
                    }
                    break;
                default:
                    $flag = false;
            }
        }

        if ($flag === false) {
            $this->error("'where' only accept more than 1 but less than 4 parameter");
        }

        if (count($whereArr) == 0) {
            return '';
        }

        $gule = ($CONCAT_WITH_OR == true)? ' OR ' : ' AND ';
        return implode($gule, $whereArr);
    }

    public function groupBy($param)
    {
        if (is_array($param) && count($param) > 0) {
            $this->groupBy = implode('`,`', $param);
            $this->groupBy = 'GROUP BY `'.$this->groupBy.'`';
        } elseif (is_string($param) && !empty($param)) {
            $this->groupBy = 'GROUP BY `'.$param.'`';
        } else {
            $this->error("'groupBy' only acccept string array or not-empty string");
            return;
        }
    }



    public function limit($limit, $offset = 0)
    {
        if (intval($limit) <= 0) {
            $this->error("'limit' is a integer over 0");
            return;
        }

        if ($offset > 0) {
            $this->offset = null;
            $this->limit = 'LIMIT '.intval($offset).','.intval($limit);
        } else {
            $this->limit = 'LIMIT '.intval($limit);
        }
    }

    public function offset($param)
    {
        if (is_numeric($param) && intval($param) >= 0) {
            if (preg_match('/^LIMIT (?:|[0-9]+ )([0-9]+)$/', $this->limit, $match)) {
                $this->limit = 'LIMIT '.intval($param).' '.intval($match[1]);
            } else {
                $this->limit = null;
                $this->offset = 'OFFSET '.$param;
            }
        } else {
            $this->error("'offset' only acccept string");
        }
    }

    public function orderBy($arg, $DESC = false)
    {
        if (is_string($arg)) {
            $this->orderBy = 'ORDER BY `'.$arg.'`';
            $this->orderBy.= ($DESC == true)? ' DESC': '';
        } elseif (is_array($arg)) {
            $arr = [];
            foreach ($arg as $item) {
                $str = '`'.$item[0].'`';
                $str.= (isset($item[1]) && $item[1] == true)? ' DESC': '';
                $arr[] = $str;
            }
            $this->orderBy = 'ORDER BY '.implode(', ', $arr);
        } else {
            $this->error("'where' only acccept array or string");
        }
    }

    private function error($msg)
    {
        $this->errorFlag = true;
        $this->errorMsg[] = $msg;
    }
}
