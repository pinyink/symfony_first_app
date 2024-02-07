<?php 
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class DataTableService {
    
    private $query;

    public function getData($entityManager, $request) {
         
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

        $db = $entityManager->getConnection();
        $sql = $this->query;

        $resultSet = $db->executeQuery($sql, []);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

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
}