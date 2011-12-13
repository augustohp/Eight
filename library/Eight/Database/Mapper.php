<?php
namespace Eight\Database;

use \Pdo;
use \UnexpectedValueException;
use \ReflectionClass;
use Respect\Relational\Mapper   as Relational;
use Respect\Relational\Db       as RelationalDb;

/**
 * Object to database mapping wrapper for Respect\Relational
 *
 * @package Eight\Database
 * @uses    Respect\Relational\Mapper
 * @since   0.1.0
 * @author  Augusto Pascutti <augusto@phpsp.org.br>
 */
class Mapper extends Relational
{
    /**
     * Returns a PDO instance of the connection used by this mapper.
     *
     * @throws  UnexpectedValueException    Could not retrieve PDO
     * @return  Pdo
     */
    public function getConnection()
    {
        if ($this->db instanceof Pdo)
            return $this->db;
        
        if ($this->db instanceof RelationalDb) {
            $reflection = new ReflectionClass($this->db);
            $property   = $reflection->getProperty('connection');
            $property->setAccessible(true);
            return $property->getValue($this->db);
        }

        throw new UnexpectedValueException("PDO connection could not be found");
    }
}