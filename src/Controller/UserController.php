<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Forms\RegisterType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
   /**
     * @Route("/registro", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
		// Crear formulario asociado a objeto user
		$user = new User();
		$form = $this->createForm(RegisterType::class, $user);
		
		// Rellenar el objeto con los datos del form
		$form->handleRequest($request);
		
		// Comprobar si el form se ha enviado y validarlo
		if($form->isSubmitted() && $form->isValid()){
			// Modificando el objeto para guardarlo
			$user->setRole('ROLE_USER');
			$user->setCreatedAt(new \Datetime('now'));
			//Encriptar y guardar contraseña utilizando encoders definidos en config/packages/security.yaml
			//NECESARIO IMPLEMENTAR interface UserInterface en entidad User
			$encoded = $encoder->encodePassword($user,$user->getPassword());
			$user->setPassword($encoded);
		
			
			// Guardar usuario
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
            $em->flush();
			
            // Redirección a task index
			return $this->redirectToRoute('task');
            
		}
		
        return $this->render('user/register.html.twig', [
			'form' => $form->createView()
        ]);
    }

	//@param utilidades de autentificación
	/**
     * @Route("/login", name="login")
     */
	public function login(AuthenticationUtils $authenticationUtils){
		$error = $authenticationUtils->getLastAuthenticationError();
		$lastUserName = $authenticationUtils->getLastUsername();

		return $this->render('user/login.html.twig', array(
			'error' => $error,
			'lastUserName' => $lastUserName
		));
	}
}

