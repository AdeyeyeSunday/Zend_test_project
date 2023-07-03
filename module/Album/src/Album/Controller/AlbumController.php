<?php 
 namespace Album\Controller;

use Album\Model\AlbumTable;
use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use Album\Model\Album;
 use Album\Form\AlbumForm;
 use Zend\Http\Request;

 class AlbumController extends AbstractActionController
 {
     protected $albumTable;
     /**
      * @var Request

      */
     protected $request;

     public function __construct(AlbumTable $albumTable)
     {
      $this->albumTable = $albumTable;
      $this->request = $this->getRequest();
     }

     public function indexAction()
     {
      return new ViewModel(array(
             'albums' => $this->albumTable->fetchAll(),
         ));
     }
     public function addAction()
     {
      $form = new AlbumForm();
      $form->get('submit')->setValue('Add');
      if ($this->request->isPost()) {
          $album = new Album();
          $form->setInputFilter($album->getInputFilter());
          $form->setData($this->request->getPost());

          if ($form->isValid()) {
              $album->exchangeArray($form->getData());
              $this->albumTable->saveAlbum($album);
              // Redirect to list of albums
              return $this->redirect()->toRoute('album');
          }
      }
      return array('form' => $form);
     }

     public function editAction()
     {
      $id = (int) $this->params()->fromRoute('id', 0);
      // var_dump($id);
      if (!$id) {
          return $this->redirect()->toRoute('album', array(
              'action' => 'add'
          ));
      }
      try {
          $album = $this->albumTable->getAlbum($id);
      }
      catch (\Exception $ex) {
          return $this->redirect()->toRoute('album', array(
              'action' => 'index'
          ));
      }

      $form  = new AlbumForm();
      $form->bind($album);
      $form->get('submit')->setAttribute('value', 'Edit');

      $request = $this->getRequest();
      if ($this->request->isPost()) {
          $form->setInputFilter($album->getInputFilter());
          $form->setData($this->request->getPost());

          if ($form->isValid()) {
              $this->albumTable->saveAlbum($album);

              return $this->redirect()->toRoute('album');
          }
      }
      return array(
          'id' => $id,
          'form' => $form,
      );
     }

     public function deleteAction()
     {
      $id = (int) $this->params("id");
      $album =  $this->albumTable->getAlbum($id);

      if (!$album) {
          return $this->redirect()->toRoute('album');
      }

      if ($this->request->isPost() && $this->request->getPost('del', 'No') == "Yes") {
              $this->albumTable->deleteAlbum($id);
          return $this->redirect()->toRoute('album');
      }

      return array(
          'id'    => $id,
          'album' => $album,
      );
     }



  
 }