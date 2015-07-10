<?php

namespace Bolt\Extension\q17acklen\popularcontent;

use Bolt\Application;
use Bolt\BaseExtension;

class Extension extends BaseExtension
{
  	public $isMobile;
  	public $ip;
  	public $userAgent;
  	public $tableName;

    public function initialize() { return;
        $this->addTwigFunction('popcon_recordContentView', 'twig_popcon_recordContentView');
        $this->addTwigFunction('popcon_getPopularContent', 'twig_popcon_getPopularContent');
        $this->tableName = $this->config['general']['database']['prefix'] . '17acklen_content_views';
        if(true)//$this->app['config']->get('general/database/driver') == 'pdo_sqlite')
        {
        	$query = "CREATE TABLE IF NOT EXISTS `$this->tableName`
	    		(
	    			`view_id` INTEGER PRIMARY KEY,
					`content_id` INTEGER NOT NULL,
					`contenttype` VARCHAR NOT NULL,
					`view_ip_address` VARCHAR NOT NULL,
					`view_browser` VARCHAR NOT NULL,
					`view_is_mobile` INTEGER(1) NOT NULL DEFAULT 0,
					`view_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
	    		)";
		}
        else
        {
        	$query = "CREATE TABLE IF NOT EXISTS `$this->tableName`
	    		(
	    			`view_id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
					`content_id` INTEGER UNSIGNED NOT NULL,
					`contenttype` VARCHAR(256) NOT NULL,
					`view_ip_address` VARCHAR(45) NOT NULL,
					`view_browser` VARCHAR(256),
					`view_is_mobile` TINYINT(1) NOT NULL DEFAULT 0,
					`view_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
					PRIMARY KEY (`view_id`),
					INDEX content_id (`content_id`),
					INDEX contenttype (`contenttype`),
					INDEX view_ip_address (`view_ip_address`),
					INDEX view_browser (`view_browser`)
	    		)";
        }
		$stmt = $this->app['db']->prepare($query);
		$res = $stmt->execute();
		$this->userAgent = $_SERVER['HTTP_USER_AGENT'];
    	$this->ip = $_SERVER['REMOTE_ADDR'];
    	$this->isMobile = $this->popcon_isMobile();
    }

    public function getName()
    {
        return "Popular Content";
    }

    /**
     * records a view of the content in the database
     * @param  object $recordOb the bolt record object
     * @return void         
     */
    public function twig_popcon_recordContentView($recordOb)
    {
    	$query = "INSERT INTO `$this->tableName` (`content_id`, `contenttype`, `view_ip_address`, `view_browser`, `view_is_mobile`)
    		VALUES(?, ?, ?, ?, ?)";
		$stmt = $this->app['db']->prepare($query);
		$stmt->bindValue(1, $recordOb->id);
		$stmt->bindValue(2, $recordOb->contenttype['name']);
		$stmt->bindValue(3, $this->ip);
		$stmt->bindValue(4, $this->userAgent);
		$stmt->bindValue(5, $this->isMobile);
		$res = $stmt->execute();
    }

    protected function popcon_isMobile()
    {
	    $mobUAs = array(
	        '/iphone/i' => 'iPhone', 
	        '/ipod/i' => 'iPod', 
	        '/ipad/i' => 'iPad', 
	        '/android/i' => 'Android', 
	        '/blackberry/i' => 'BlackBerry', 
	        '/BB/i' => 'BlackBerry', 
	        '/webos/i' => 'Mobile'
	    );

	    //Return true if Mobile User Agent is detected
	    foreach($mobUAs as $key => $value)
	    {
	        if(preg_match($key, $_SERVER['HTTP_USER_AGENT']))
	        {
	            return TRUE;
	        }
	    }
	    //Otherwise return false..  
	    return FALSE;
	}

	public function twig_popcon_getPopularContent($numResults = 10, $contenttype = NULL)
	{
		$ctype = ($contenttype) ? $contenttype : '%';
		$numRes = (is_numeric($numResults)) ? (int) $numResults : 10;
		$query = "SELECT COUNT(pcv.view_id) AS `viewCnt`, pcv.contenttype, pcv.content_id
			FROM `$this->tableName` pcv
			WHERE pcv.contenttype LIKE ?
			GROUP BY pcv.contenttype, pcv.content_id ORDER BY COUNT(pcv.view_id) DESC LIMIT $numRes";
		$stmt = $this->app['db']->prepare($query);
		$stmt->bindValue(1, $ctype);
		$han = $stmt->execute();
		$res = $stmt->fetchAll();
		return $res;
	}

	public function isSafe()
	{
		return TRUE;
	}

}






