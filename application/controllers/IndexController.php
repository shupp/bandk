<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction() 
    {
        $model = new Model_Images();
        $this->view->boobsImage = $model->getBoobsImage();
        $this->view->kittensImage = $model->getKittensImage();
    }
}
