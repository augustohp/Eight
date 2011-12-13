<?php
namespace Eight\Privacy;

use \ReflectionClass            as RClass;
use \ReflectionObject           as RObject;
use \ReflectionProperty         as RProperty;
use \UnexpectedValueException   as Value;
use \InvalidArgumentException   as Argument;
use Eight\Entity\Owner;

/**
 * Interface for the privacy control of information.
 *
 * @package Eight\Privacy
 * @since   0.1.0
 * @author  Augusto Pascutti <augusto@phpsp.org.br>
 */
class Control
{   
    protected $entity;
    protected $owner;
    private $key;

	const VISIBLE = 'brazil';
	const ENCODED = 'japan';
    
    /**
     * undocumented function
     *
     * @param Eight\Entity\Owner    $owner 
     * @param Object                $entity 
     */
    public function __construct(Owner $owner, $entity=null)
    {
        $this->owner  = $owner;
		$this->getPrivacyKey();
		if (!is_null($entity))
        	$this->setEntity($entity);
    }

	public function setEntity($entity)
	{
		$this->entity = $entity;
	}
    
    /**
     * Makes information on the entity availiable to everyone wanting to 
     * read it.
     *
     * @return Object
     */
    public function decode($entity=null)
    {
		if (!is_null($entity))
			$this->setEntity($entity);
        $this->changePrivacy(self::VISIBLE);
		return $this->entity;
    }
    
    /**
     * Makes information on the entity only availiable to someone who knows the
     * privacy key for that information.
     *
     * @return Object
     */
    public function encode($entity=null)
    {
		if (!is_null($entity))
			$this->setEntity($entity);
        $this->changePrivacy(self::ENCODED);
		return $this->entity;
    }
    
    /**
     * Gets an array with the  ReflectionProperties that are affected by privacy
     * control.
     *
     * @return array
     */
    protected function getPropertiesAffected()
    {
        $refleFilter = RProperty::IS_PUBLIC | RProperty::IS_PROTECTED;
        $reflection  = new RObject($this->entity);
        $filter      = array('id', 'owner_id', 'created');
        $change      = array();
        foreach ($reflection->getProperties() as $property) {
            if (in_array($property->name, $filter)) { continue; }
            
            $change[] = $property;
        }
        return $change;
    }
    
    protected function getPrivacyKey()
    {
        if ($this->key)
            return $this->key;

        if (isset($this->owner->name) && isset($this->owner->salt))
            return $this->key = $this->owner->name.$this->owner->name;

        throw new Value('Owner does not has the mandatory attributes');
    }
    
    protected function changePrivacy($privacy)
    {
        $cipher  = MCRYPT_RIJNDAEL_256;
        $mode    = MCRYPT_MODE_ECB;
        $key     = $this->getPrivacyKey();
        $iv_size = mcrypt_get_iv_size($cipher, $mode);
        $iv      = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        foreach ($this->getPropertiesAffected() as $property) {
            $property->setAccessible(true);
            $from = (string) $property->getValue($this->entity);
            if ($privacy == self::ENCODED)
                $to = mcrypt_encrypt($cipher, $key, $from, $mode, $iv);
            else if ($privacy == self::VISIBLE)
                $to = mcrypt_decrypt($cipher, $key, $from, $mode, $iv);
			else
				throw new Argument('Privacy change option not recognized');
            $property->setValue($this->entity, $to);
        }
    }
}