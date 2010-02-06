<?php

class ImageController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
    }
    public function displayAction() 
    {
        $boobs   = $this->_getParam('boobs', null);
        $kittens = $this->_getParam('kittens', null);

        $mogileKey = null;
        if ($boobs !== null) {
            $mogileKey = Model_Images::TYPE_BOOBS . '-' . $boobs;
        } else if ($kittens !== null) {
            $mogileKey = Model_Images::TYPE_KITTENS . '-' . $kittens;
        }

        if ($mogileKey == null) {
            throw new Exception('Invalid request');
        }

        $model = new Model_Images();
        $image = $model->getImage($mogileKey);
        $this->getResponse()->setHeader('Content-Type', 'image/jpeg');
        $this->getResponse()->setHeader('Content-Length', strlen($image));
        $this->getResponse()->setBody($image);
    }
}
