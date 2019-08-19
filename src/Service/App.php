<?php


namespace Service;


use Doctrine\ORM\EntityManagerInterface;
use Entity\User;

class App {

    public static function is_loggedin(EntityManagerInterface $entityManager) {
        if (isset($_SESSION['auth'])){
            /** @var User $user */
            $user = $entityManager->getRepository('Entity\\User')->findOneBy(['id' => $_SESSION['auth']]);
            return ($user == null ? false : $user);
        }
        return false;
    }
}