<?php
namespace Eight;

use Respect\Rest\Routable;
/**
 * All routes must extend this class
 *
 * @todo    Decouple this more, inheriting a whole class is insane for this
 * @package Eight
 * @since   0.1.0
 * @author  Augusto Pascutti <augusto@phpsp.org.br>
 */
abstract class Route implements Routable
{
    /**
     * @var Eight\Application
     */
    protected $app;
    
    /**
     * Receives the application instance.
     *
     * @param   Eight\Application   $app 
     */
    final public function __construct(Application $app=null)
    {
        if (!is_null($app))
            $this->app = $app;
    }
}