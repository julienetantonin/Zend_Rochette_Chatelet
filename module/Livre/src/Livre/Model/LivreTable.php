<?php

namespace Album\Model;

use Zend\Db\TableGateway\TableGateway;

class AlbumTable {

    protected $tableGateway;
    protected $user_id;

    public function __construct(TableGateway $tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
    public function getAlbumByUser($user) {
        // $this->isMine();
        $id = (int) $user->id;
        $rowset = $this->tableGateway->select(array('User_id' => $id));
        
         if (!$rowset) {
          throw new \Exception("Could not find row $id");
          } 
        return $rowset;
    }

    public function getAlbum($id) {
        // $this->isMine();
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        /* if (!$row) {
          throw new \Exception("Could not find row $id");
          } */
        return $row;
    }

    public function saveAlbum(Album $Livre) {
        $data = array(
            'artist' => $Livre->artiste_livre,
            'title' => $Livre->title_livre,
            'owner' => $this->user_id,
        );
        $id = (int) $Livre->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAlbum($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Album id does not exist');
            }
        }
    }

    public function deleteAlbum($id) {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

}
