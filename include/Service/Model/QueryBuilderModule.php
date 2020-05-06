<?php
namespace Service\Model;

trait QueryBuilderModule
{
    protected $query;

    public static function select()
    {
        $obj = new self;
        $query = new QueryBuilder();
        $query->table(static::TABLE_NAME);
        $obj->query = $query;
        return $obj;
    }

    //查多条
    public function findAll()
    {
        list($sql,$data) = $this->query->build();
        $stat = $this->execute($sql, $data);
        return $stat->fetchAll(\PDO::FETCH_ASSOC);
    }

    //查一条
    public function find()
    {
        list($sql,$data) = $this->query->build();
        $stat = $this->execute($sql, $data);
        return $stat->fetch(\PDO::FETCH_ASSOC);
    }

    public function value()
    {
        list($sql,$data) = $this->query->build();
        $stat = $this->execute($sql, $data);
        return $stat->fetchColumn();
    }

    private function execute($sql, $data)
    {
        $stat = $this->db->prepare($sql);
        $stat->execute($data);
        return $stat;
    }

    //指定选查询的域
    public function field(...$args)
    {
        $this->query->field(...$args);
        return $this;
    }

    public function where(...$args)
    {
        $this->query->where(...$args);
        return $this;
    }

    public function orWhere(...$args)
    {
        $this->query->orWhere(...$args);
        return $this;
    }

    public function limit(...$args)
    {
        $this->query->limit(...$args);
        return $this;
    }

    public function offset(...$args)
    {
        $this->query->offset(...$args);
        return $this;
    }

    public function orderBy(...$args)
    {
        $this->query->orderBy(...$args);
        return $this;
    }

    public function groupBy(...$args)
    {
        $this->query->groupBy(...$args);
        return $this;
    }
}
