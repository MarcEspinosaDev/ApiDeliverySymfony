<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Rest\Route("/user")
 */
class UserController extends AbstractFOSRestController
{
    private $userRespository;
    private $hasher;

    public function __construct(UserRepository $repo,UserPasswordHasherInterface $hasher){
        $this->userRespository = $repo;
        $this->hasher = $hasher;
    }

    //CRUD
    /**
     * @Rest\Post(path="/")
     * @Rest\View (serializerGroups={"user"}, serializerEnableMaxDepthChecks=true)
     */
    public function createUser(Request $request){
        $user = $request->get('user');
        $role = $request->get('role');
        $form = $this->createForm(UserType::class);
        $form->submit($user);
        if(!$form->isSubmitted() || !$form->isValid()){
            return$form;
        }
        /**
         * @var User $newUser
         */
        $newUser = $form->getData();
        $roles[]= $role;
        $newUser->setRoles($roles);
        //codificar pass
        $hassedPass = $this->hasher->hashPassword(
            $newUser,
            $user['password']
        );
        $newUser->setPassword($hassedPass);
        $this->userRespository->add($newUser, true);
        return $newUser;
    }
}