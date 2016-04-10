<?php 
class DbHelper2 {
    
    private $db;
    
    public function getDb()
    {
        return $this->db;
    }

    //Gets n2manager DB connection
    public function getConnectionDb()
    {
     
    $params = array(
        'host'           => 'localhost',
        'username'       => 'spoorer1',
        'password'       => '.i$W!:3+5@@V"/v',
        'dbname'         => 'spoora'
    );

        try {
           
            $this->db = Zend_Db::factory('Mysqli', $params);
             $this->db->getConnection();
             return $this->db;
            
        } catch (Zend_Db_Adapter_Exception $e) {
            echo "DB Login error";
         // perhaps a failed login credential, or perhaps the RDBMS is not running
        } catch (Zend_Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
          echo "DB connection unknow error ".$e;
        }
      
    }
    
    //Gets n2websms DB connection
     public function getConnection_n2websmsDB()
    {
     
    $params = array(
        'host'           => 'localhost',
        'username'       => 'n2user3',
        'password'       => 'Kb#09$N#5,W75Om',
        'dbname'         => 'zumbaleh'
    );

        try {
           
            $this->db = Zend_Db::factory('Mysqli', $params);
             $this->db->getConnection();
             return $this->db;
            
        } catch (Zend_Db_Adapter_Exception $e) {
            echo "DB Login error";
         // perhaps a failed login credential, or perhaps the RDBMS is not running
        } catch (Zend_Exception $e) {
          // perhaps factory() failed to load the specified Adapter class
          echo "DB connection unknow error ".$e;
        }
      
    }
    
}
?>
