<?php

namespace DTIAcl;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module {
	
	public function onBootstrap(MvcEvent $e) {
		$eventManager = $e->getApplication ()->getEventManager ();
		$moduleRouteListener = new ModuleRouteListener ();
		$moduleRouteListener->attach ( $eventManager );
	}
	 
	public function getConfig() {
		return include __DIR__ . '/../../config/module.config.php';
	}
	public function getAutoloaderConfig() {
		return array (
				'Zend\Loader\StandardAutoloader' => array (
						'namespaces' => array (
								__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__ 
						) 
				) 
		);
	}
	public function getServiceConfig() {
	
		return array(
				'factories' => array(
						'DTIAcl\Form\Role' => function($sm)	{
							$em = $sm->get('Doctrine\ORM\EntityManager');
							$repo = $em->getRepository('DTIAcl\Entity\Role');
							$parent = $repo->fetchParent();
	
							return new Form\Role('role',$parent);
						},
						'DTIAcl\Form\Privilege' => function($sm) {
							$em = $sm->get('Doctrine\ORM\EntityManager');
							$repoRoles = $em->getRepository('DTIAcl\Entity\Role');
							$roles = $repoRoles->fetchParent();
	
							$repoResources = $em->getRepository('DTIAcl\Entity\Resource');
							$resources = $repoResources->fetchPairs();
	
							return new Form\Privilege("privilege", $roles, $resources);
						},
	
						'DTIAcl\Service\Role' => function($sm) {
							return new Service\Role($sm->get('Doctrine\ORM\Entitymanager'));
						},
						'DTIAcl\Service\Resource' => function($sm) {
							return new Service\Resource($sm->get('Doctrine\ORM\Entitymanager'));
						},
						'DTIAcl\Service\Privilege' => function($sm) {
							return new Service\Privilege($sm->get('Doctrine\ORM\Entitymanager'));
						},
	
						'DTIAcl\Permissions\Acl' => function($sm) {
							$em = $sm->get('Doctrine\ORM\EntityManager');
	
							$repoRole = $em->getRepository("DTIAcl\Entity\Role");
							$roles = $repoRole->findAll();
	
							$repoResource = $em->getRepository("DTIAcl\Entity\Resource");
							$resources = $repoResource->findAll();
	
							$repoPrivilege = $em->getRepository("DTIAcl\Entity\Privilege");
							$privileges = $repoPrivilege->findAll();
	
							return new Permissions\Acl($roles,$resources,$privileges);
						}
				)
		);
	
	}
}
