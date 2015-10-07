<?php
namespace BOS\ApiBundle\Services;
	
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ApiService
{
	/**
	 * @var ContainerInterface
	 */
	private $container;
	/**
	 * @var EntityManager 
	 */
	private $em;
	
	private $render;
	
	private $router;
	
	public function __construct(ContainerInterface $container){
		$this->container = $container;
		$this->render = $this->container->get('templating');
		$this->router = $this->container->get('router');
	}
	
	public function onKernelRequest(GetResponseEvent $event)
	{
		$request = $event->getRequest();
		$method = $request->attributes->get('_controller');
		$class_full = explode("::", $method);
		if(sizeof($class_full)!=2){
			return;
		}
		$class = trim($class_full[0]);
		$reflectionClass = new \ReflectionClass($class);		
		$reader = new AnnotationReader();
		$hdata = $reader->getClassAnnotation($reflectionClass, 'BOS\\ApiBundle\\Annotations\\BOSApiController');
		if($hdata){
			$i = -1;
			$methods = array();
			$methodsArray = $reflectionClass->getMethods();
			foreach($methodsArray as $m){
				$currentFullMethod = $class . "::" . $m->getName();
				$collection = $this->router->getRouteCollection()->all();
				$url = null;
				foreach ($collection as $route => $params){
					$defaults = $params->getDefaults();
					if(isset($defaults['_controller'])){
						if($currentFullMethod==$defaults['_controller']){
							$url = $this->router->generate($route);
						}
					}
				}
				//die("ffFF");
				$rm = new \ReflectionMethod($class . "::" . $m->getName());
				$data = $reader->getMethodAnnotation($rm, 'BOS\\ApiBundle\\Annotations\\BOSApiMethod');
				if($data){
					/* Method data, POST OR GET OR ANY */
					$mData = $reader->getMethodAnnotation($rm, "Sensio\\Bundle\\FrameworkExtraBundle\\Configuration\\Method");
					$nMethods = array();
					if($mData){
						$nMethods = $mData->getMethods();
					}
					if(!$url){
						die("BOSApiBundle fatal error: couldn't generate route for: " . $rm->getName());
					}
					$i++;
					$data->url = $url;
					$data->methods = $nMethods;
					$methods[$i] = $data;
				}
			}
			
			$message = $this->render->render("BOSApiBundle:Default:index.html.twig", array("api_header" => $hdata, "api_methods" => $methods));
			$response = new Response($message);
			$event->setResponse($response);
		}
	}
	
}