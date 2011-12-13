<?php
namespace Eight\Database\Schema;

use \Pdo;
use \UnexpectedValueException   as Value;
use Eight\Version;
/**
 * Base class that must be extended by future version of database upgrade 
 * classes.
 *
 * @package     Eight\Database
 * @subpackage  Schema
 * @since       0.1.0
 * @author      Augusto Pascutti <augusto@phpsp.org.br>
 */
abstract class Base
{
    /**
     * @var Pdo
     */
    protected $mapper;
    
    /**
     * Returns an array of queries to be executed.
     *
     * @return array
     */
    abstract public function getSql();
    /**
     * Returns the version of the database.
     *
     * @return integer
     */
    abstract public function getVersion();
    
    /**
     * Constructor.
     *
     * @param   Eight\Database\Mapper   $mapper 
     */
    public function __construct(Pdo $mapper=null)
    {
        $this->mapper = $mapper;
    }
    
    /**
     * Returns the SQL to be executed in the database.
     *
     * @throws  UnexpectedValueException    Version of database not specified
     * @throws  UnexpectedValueException    SQLs not returned in an array
     * @return  string
     */
    final private function getSqlToExecute()
    {
        $version = $this->getVersion();
        $api     = Version::API;
        if (empty($version))
            throw new Value('Version must have a value');
        
        $sqls    = $this->getSql();
        if (!is_array($sqls))
            throw new Value('SQLs to be executed in database upgrade should be in an array');

        $sqls[]  = "REPLACE version SET version.database = {$version}, api = '{$api}'";
        return $sqls;
    }
    
    /**
     * Executes the changes into the database.
     */
    final public function execute()
    {
        $connection = $this->mapper;
        if (!$connection instanceof Pdo)
            throw new Value('PDO instance expected');
        
        foreach ($this->getSqlToExecute() as $sql)
            $connection->exec($sql);
    }
}