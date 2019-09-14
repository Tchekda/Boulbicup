<?php


namespace Service;


use Doctrine\ORM\EntityManagerInterface;
use Entity\User;

class App {
    /**
     * @param EntityManagerInterface $entityManager
     * @return bool|User
     * Checks if the visitor is loggedin and exists in the database
     */
    public static function is_loggedin(EntityManagerInterface $entityManager) {
        if (isset($_SESSION['auth'])){ // If auth Id stored in Session
            /** @var User $user */
            $user = $entityManager->getRepository('Entity\\User')->findOneBy(['id' => $_SESSION['auth']]); // Check If ID registered in database
            if ($user == null){ // if not, delete session
                session_destroy();
                return false;
            }else { // If exists
                return $user; // return User object
            }
        }
        return false;
    }
}