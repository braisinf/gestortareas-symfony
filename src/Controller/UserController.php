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
     * @Route("/user/register", name="register")
     */
    public function register(Request $request)
    {
		// Crear formulario asociado a objeto user
		$user = new User();
		$form = $this->createForm(RegisterType::class, $user);
		
		// Rellenar el objeto con los datos del form
		$form->handleRequest($request);
		
		// Comprobar si el form se ha enviado y si es válido
		if($form->isSubmitted() && $form->isValid()){
			// Modificando el objeto para guardarlo
			$user->setRole('ROLE_USER');
			$user->setCreatedAt(new \Datetime('now'));
			
			// Cifrar contraseña 
			$hash = password_hash($user->getPassword(), PASSWORD_BCRYPT, ['cost' => 12]);
            //Setear contraseña cifrada
            $user->setPassword($hash);
			
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
	
}

