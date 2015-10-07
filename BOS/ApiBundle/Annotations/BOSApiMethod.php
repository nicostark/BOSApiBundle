<?php
namespace BOS\ApiBundle\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 *
 * @author nbullorini
 * @Annotation
 * @Target("METHOD")
 */
class BOSApiMethod extends Annotation
{
	public $methods = "";
	public $title = "";
	public $description = "";
	public $parameters = "";	
	public $url = "";
}