<?php
namespace Eight\Database;

use \Pdo;
use \DirectoryIterator;
use \UnexpectedValueException;

/**
 * Keeps the database updated.
 *
 * @package Eight\Database
 * @since   0.1.0
 * @author  Augusto Pascutti <augusto@phpsp.org.br>
 */
class Updater
{
    /**
     * @var Pdo
     */
    protected $pdo;
    
    /**
     * Constructor.
     *
     * @param   Pdo     $c
     */
    public function __construct(Pdo $c)
    {
        $this->pdo = $c;
    }
    
    /**
     * Returns the current database version.
     *
     * @return int
     */
    public function getCurrentVersion()
    {
        // Checks if there are any tables at all
        $st  = $this->pdo->prepare('SHOW TABLES LIKE \'version\'');
        $st->execute();
        $all = $st->fetchAll();
        if (count($all) == 0)
            return 0;
        
        // Gets the current version from table
        $st  = $this->pdo->prepare('SELECT v.database FROM version v');
        $st->execute();
        $all = $st->fetch(PDO::FETCH_ASSOC);
        return $all['database'];
    }
    
    /**
     * Returns all versions of database availiable.
     * The array key is the version number and the value the classname.
     *
     * @return array
     */
    protected function getVersions()
    {
        $versions = array();
        $iterator = new DirectoryIterator(__DIR__.DS.'Schema');
        $ignore   = array('.', '..', 'Base.php');
        foreach ($iterator as $file) {
            $filename  = (string) $file;
            if (in_array($filename, $ignore)) { continue; }
            
            $classname = 'Eight\Database\Schema\\'.rtrim($filename, '.php');
            $object    = new $classname();
            $version   = $object->getVersion();
            if (!is_scalar($version))
                throw new Value("Database Version of '{$classname}' is not scalar");
            $versions[$version] = $classname;
        }
        ksort($versions);
        return $versions;
    }
    
    /**
     * Syncronises the database.
     *
     * @return void
     */
    public function sync()
    {
        $current    = $this->getCurrentVersion();
        $availiable = $this->getVersions();
        $this->pdo->beginTransaction();
        foreach ($availiable as $version=>$classname) {
            if ($version <= $current) { continue; }
            $object = new $classname($this->pdo);
            $object->execute();
        }
        $this->pdo->commit();
    }
}