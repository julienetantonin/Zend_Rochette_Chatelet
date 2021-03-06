<?php 
 namespace Album\Controller;
 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use Album\Model\Album;          // <-- Add this import
 use Album\Form\AlbumForm;       // <-- Add this import
 class AlbumController extends AbstractActionController
 {
     public function indexAction()
     {
		 
		//$this->layout()->setVariable('hasIdentity', $this->getServiceLocator()->get('AuthService')->hasIdentity());
/*
        if (! $this->getServiceLocator()
                 ->get('AuthService')->hasIdentity()){
            return $this->redirect()->toRoute('login');
        }
*/
		// print_r( $this->getServiceLocator()->get('AuthService'));
		//exit;
         return new ViewModel(array(
			
             'albums' => $this->getAlbumTable()->getAlbumByUser($this->getServiceLocator()->get('SanAuth\Model\MyAuthStorage')->read()),
         ));
     }
    public function addAction()
     {
         $form = new AlbumForm();
         $form->get('submit')->setValue('Add');
         $request = $this->getRequest();
         if ($request->isPost()) {
             $album = new Album();
             $form->setInputFilter($album->getInputFilter());
             $form->setData($request->getPost());
             if ($form->isValid()) {
                 $album->exchangeArray($form->getData());
                 $this->getAlbumTable()->saveAlbum($album);
                 // Redirect to list of albums
                 return $this->redirect()->toRoute('album');
             }
         }
         return array('form' => $form);
     }
     public function editAction()
     {
         $id = (int) $this->params()->fromRoute('id', 0);
         if (!$id) {
             return $this->redirect()->toRoute('album', array(
                 'action' => 'add'
             ));
         }
         // Get the Album with the specified id.  An exception is thrown
         // if it cannot be found, in which case go to the index page.
         try {
             $album = $this->getAlbumTable()->getAlbum($id);
         }
         catch (\Exception $ex) {
             return $this->redirect()->toRoute('album', array(
                 'action' => 'index'
             ));
         }
		 if(!$album) {
             return $this->redirect()->toRoute('album', array(
                 'action' => 'add'
             ));
		 }
		 // var_dump($album);
		 
         $form  = new AlbumForm();
         $form->bind($album);
         $form->get('submit')->setAttribute('value', 'Edit');
         $request = $this->getRequest();
         if ($request->isPost()) {
             $form->setInputFilter($album->getInputFilter());
             $form->setData($request->getPost());
             if ($form->isValid()) {
                 $this->getAlbumTable()->saveAlbum($album);
                 // Redirect to list of albums
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
         $id = (int) $this->params()->fromRoute('id', 0);
		 $album = $this->getAlbumTable();
         if (!$id) {
             return $this->redirect()->toRoute('album');
         }
		 
		 if(!$album->getAlbum($id))
             return $this->redirect()->toRoute('album');
         $request = $this->getRequest();
         if ($request->isPost()) {
             $del = $request->getPost('del', 'No');
             if ($del == 'Yes') {
                 $id = (int) $request->getPost('id');
                 $album->deleteAlbum($id);
             }
             // Redirect to list of albums
             return $this->redirect()->toRoute('album');
         }
         return array(
             'id'    => $id,
             'album' => $album->getAlbum($id)
         );
     }
	 
	 protected $albumTable;
	 
     public function getAlbumTable()
     {
         if (!$this->albumTable) {
             $sm = $this->getServiceLocator();
             $this->albumTable = $sm->get('Album\Model\AlbumTable');
			 
         }
         return $this->albumTable;
     }
 }
 
 