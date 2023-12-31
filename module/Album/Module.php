<?php
 namespace Album;
 use Album\Model\Album;
 use Album\Model\AlbumTable;
 use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
 use Zend\ModuleManager\Feature\ConfigProviderInterface;
 use Zend\Db\ResultSet\ResultSet;
 use Zend\Db\TableGateway\tableGateway;
 use Album\Controller\AlbumController;

 class Module implements AutoloaderProviderInterface, ConfigProviderInterface
 {
     public function getAutoloaderConfig()
     {
         return array(
             'Zend\Loader\ClassMapAutoloader' => array(
                 __DIR__ . '/autoload_classmap.php',
             ),
             'Zend\Loader\StandardAutoloader' => array(
                 'namespaces' => array(
                     __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                 ),
             ),
         );
     }

     public function getConfig()
     {
         return include __DIR__ . '/config/module.config.php';
     }

     public function getServiceConfig()
     {
         return array(
             'factories' => array(
                 'Album\Model\AlbumTable' =>  function($sm) {
                     $tableGateway = $sm->get('AlbumTableGateway');
                    //  var_dump($tableGateway);
                    //  exit();
                     $table = new AlbumTable($tableGateway);
                     return $table;
                 },
                 'AlbumTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new Album());
                     return new TableGateway('album', $dbAdapter, null, $resultSetPrototype);
                 },
             ),
         );


     }

     public function getControllerConfig()
     { 
      return array(
       'factories' => array(
        AlbumController::class => function($sm){
        $sm = $sm->getServiceLocator();
        $table = $sm -> get("Album\Model\AlbumTable");
        // $request = $sm->get()
        return new AlbumController($table);
      }   
       )
      );

     }
 }