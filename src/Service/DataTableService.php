<?php 
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class DataTableService {
    
    private $table;
    private $query;
    private $where;
    private $columnSearch = [];
    private $columnOrder = [];

    /**
     * Set the value of query
     *
     * @return  self
     */ 
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Set the value of columnSearch
     *
     * @return  self
     */ 
    public function setColumnSearch($columnSearch)
    {
        $this->columnSearch = $columnSearch;

        return $this;
    }

    /**
     * Set the value of columnOrder
     *
     * @return  self
     */ 
    public function setColumnOrder($columnOrder)
    {
        $this->columnOrder = $columnOrder;

        return $this;
    }

    /**
     * Set the value of table
     *
     * @return  self
     */ 
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Set the value of where
     *
     * @return  self
     */ 
    public function setWhere($where)
    {
        $this->where = $where;

        return $this;
    }

    public function getData($entityManager, $request) 
    {
        $params = $request->request->all();
        // // Get the parameters from the Ajax Call
        // if ($request->getMethod() == 'POST') {
        //     $parameters = $request->request->all();
        //     $draw = $parameters['draw'];
        //     $start = $parameters['start'];
        //     $length = $parameters['length'];
        //     $search = $parameters['search'];
        //     $orders = $parameters['order'];
        //     $columns = $parameters['columns'];
        // }
        // else
        //     die;

        $columnSearch = $this->columnSearch;
        $columOrder = $this->columnOrder;

        $db = $entityManager->getConnection();

        $queryFiltered = $this->query;
        
        // get charachter 
        $strPost = strpos($queryFiltered, "from $this->table");
        // get select until before from
        $str = substr($queryFiltered, 0, $strPost);
        $queryFiltered = str_replace($str, "select count(*) total ", $this->query);

        $queryCount = "select count(*) total from $this->table";

        $query = $this->query;

        $queryValue = [];
        $paramValue = [];
        $ifWhere = 0;
        if (!empty($this->where)) {
            $query .= " WHERE 1=1 ";
            $queryFiltered .= " WHERE 1=1 ";
            foreach ($this->where as $key => $value) {
                $query .= "AND $key = :$key ";
                $queryFiltered .= "AND $key = :$key ";
                $queryValue[$key] = $value;
                $paramValue[$key] = $value;
            }
            $ifWhere = 1;
        }

        foreach ($columnSearch as $kSearch => $vSearch) {
            if (isset($params['search']['value'])) {
                if ($ifWhere == 0) {
                    $query .= " WHERE 1=1 ";
                    $queryFiltered .= " WHERE 1=1 ";
                }
                foreach ($params['search']['value'] as $key => $value) {
                    $query .= "AND $key like '%:$key%' ";
                    $queryFiltered .= "AND $key like '%:$key%' ";
                    $queryValue[$key] = $value;
                    $paramValue[$key] = $value;
                }
            }
        }
        if (isset($params['order'])) {
            $query .= " order by ".$params['order']['0']['column']." ".$params['order']['0']['dir'];
        }
        if (isset($params['length']) && $params['length'] != -1) {
            $query .= " limit ? offset ?" ;
            array_push($queryValue, $params['length']);
            array_push($queryValue, $params['start']);
        }

        $resultSet = $db->executeQuery($query, $queryValue);
        $resultFilter = $db->executeQuery($queryFiltered, $paramValue);
        $resultCount = $db->executeQuery($queryCount, []);
        // returns an array of arrays (i.e. a raw data set)
        $data = $resultSet->fetchAllAssociative();
        $filter = $resultFilter->fetchAllAssociative();
        $count = $resultCount->fetchAllAssociative();
        return [
            'data' => $data,
            'filter' => $filter[0],
            'count' => $count[0],
        ];

    }
}