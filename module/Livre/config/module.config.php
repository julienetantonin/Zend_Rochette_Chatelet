<?php 
 return array(
     'controllers' => array(
         'invokables' => array(
             'Album\Controller\Album' => 'Film\Controller\FilmController',
         ),
     ),
	 
     'router' => array(
         'routes' => array(
             'Livre' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/Livre[/:action][/:id]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'id'     => '[0-9]+',
                     ),
                     'defaults' => array(
                         'controller' => 'Album\Controller\Album',
                         'action'     => 'index',
                     ),
                 ),
             ),
         ),
     ),
	 
     'view_manager' => array(
         'template_path_stack' => array(
             'Livre' => __DIR__ . '/../view',
         ),
     ),
 );
 
 