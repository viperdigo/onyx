<?php

namespace Worldplay\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ServiceController extends Controller
{
    public function flashAction($type, $message){

        switch ($type){
            case 's':
                $type = 'success';
                break;
            case 'i':
                $type = 'info';
                break;
            case 'w':
                $type = 'warning';
                break;
            case 'd':
                $type = 'danger';
                break;
        }

        $this->get('session')->getFlashBag()->add(
            $type,
            $message
        );
    }
}
