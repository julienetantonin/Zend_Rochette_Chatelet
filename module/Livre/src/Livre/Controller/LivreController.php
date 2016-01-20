<?php 
 namespace Film\Controller;
 use Zend\Mvc\Controller\AbstractActionController;
 use Zend\View\Model\ViewModel;
 use Film\Model\Film;          // <-- Add this import
 use Film\Form\FilmForm;       // <-- Add this import
 class FilmController extends AbstractActionController
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
			
             'Livres' => $this->getAlbumTable()->getAlbumByUser($this->getServiceLocator()->get('SanAuth\Model\MyAuthStorage')->read()),
         ));
     }
    public function addAction()
     {
         $form = new AlbumForm();
         $form->get('submit')->setValue('Add');
         $request = $this->getRequest();
         if ($request->isPost()) {
             $Livre = new Album();
             $form->setInputFilter($Livre->getInputFilter());
             $form->setData($request->getPost());
             if ($form->isValid()) {
                 $Livre->exchangeArray($form->getData());
                 $this->getAlbumTable()->saveAlbum($Livre);
                 // Redirect to list of Livres
                 return $this->redirect()->toRoute('Livre');
             }
         }
         return array('form' => $form);
     }
     public function editAction()
     {
         $id = (int) $this->params()->fromRoute('id', 0);
         if (!$id) {
             return $this->redirect()->toRoute('Livre', array(
                 'action' => 'add'
             ));
         }
         // Get the Album with the specified id.  An exception is thrown
         // if it cannot be found, in which case go to the index page.
         try {
             $Livre = $this->getAlbumTable()->getAlbum($id);
         }
         catch (\Exception $ex) {
             return $this->redirect()->toRoute('Livre', array(
                 'action' => 'index'
             ));
         }
		 if(!$Livre) {
             return $this->redirect()->toRoute('Livre', array(
                 'action' => 'add'
             ));
		 }
		 // var_dump($Livre);
		 
         $form  = new AlbumForm();
         $form->bind($Livre);
         $form->get('submit')->setAttribute('value', 'Edit');
         $request = $this->getRequest();
         if ($request->isPost()) {
             $form->setInputFilter($Livre->getInputFilter());
             $form->setData($request->getPost());
             if ($form->isValid()) {
                 $this->getAlbumTable()->saveAlbum($Livre);
                 // Redirect to list of Livres
                 return $this->redirect()->toRoute('Livre');
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
		 $Livre = $this->getAlbumTable();
         if (!$id) {
             return $this->redirect()->toRoute('Livre');
         }
		 
		 if(!$Livre->getAlbum($id))
             return $this->redirect()->toRoute('Livre');
         $request = $this->getRequest();
         if ($request->isPost()) {
             $del = $request->getPost('del', 'No');
             if ($del == 'Yes') {
                 $id = (int) $request->getPost('id');
                 $Livre->deleteAlbum($id);
             }
             // Redirect to list of Livres
             return $this->redirect()->toRoute('Livre');
         }
         return array(
             'id'    => $id,
             'Livre' => $Livre->getAlbum($id)
         );
     }
	 
	 protected $LivreTable;
	 
     public function getAlbumTable()
     {
         if (!$this->LivreTable) {
             $sm = $this->getServiceLocator();
             $this->LivreTable = $sm->get('Album\Model\AlbumTable');
			 
         }
         return $this->LivreTable;
     }
 }
 
 