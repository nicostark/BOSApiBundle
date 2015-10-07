<?php
namespace BOS\ApiBundle\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * 
 * @author nbullorini
 * @Annotation
 * @Target("CLASS")
 */
class BOSApiController extends Annotation
{
	public $title = "";
	public $description = "";
	public $version = "";
	public $base = "";
	public $http_headers_title = "";
	public $http_headers_description = "";
	public $http_headers = "";
}