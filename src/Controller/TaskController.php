<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Task;
use App\Forms\TaskType;
use Symfony\Component\Security\Core\User\UserInterface;


class TaskController extends AbstractController
{
    /**
     * @return=View All Tasks
     * @Route("/", name="task")
     */
    public function index()
    {   
        //Entity Manager
        $em = $this->getDoctrine()->getManager();
        //Repositorio (clase)
        $task_repo=$this->getDoctrine()->getRepository(Task::class);
        //Guardar todos las tareas en array de objetos tasks ordenadas por id de forma descendente
        $tasks=$task_repo->findBy([],['id'=>'DESC']);

        
        
        return $this->render('task/index.html.twig', [
            'tasks' => $tasks
        ]);
    }

     /**
      * @return=View User Tasks
      * @param=Usuario autenticado
     * @Route("/user/tasks/{id}", name="user_tasks")
     */ 
    public function getUserTasks(UserInterface $user){
        //Guardar todos las tareas asignadas al usuario en array de objetos tasks  
        //Conexión
        $em = $this->getDoctrine()->getManager();
        $db = $em->getConnection();
        //Consulta
        $email=$user->getEmail();
        $query = "SELECT * FROM tasks WHERE email='".$email."'Order By id desc;";
        $stmt = $db->prepare($query);
        $params = array();
        $stmt->execute($params);
        $tasks=$stmt->fetchAll();

         //$tasks=
         return $this->render('task/userTasks.html.twig',[
            'tasks' => $tasks
        ]);
    }

    //@param=id tarea deseada 
    //@return=View User Task
    /**
     * @Route("/task/detail/{id}", name="detail")
     */ 
    public function detail(Task $task){
        if(!$task){
            return $this->redirectToRoute('/');
        }else{
            return $this->render('task/detail.html.twig',[
                'task' => $task
            ]);
        }
    }



   
      /**
       * @param=Request formulario crear tarea, Objeto user
       * @return=Form Create Task
     * @Route("/task/create", name="create_task")
     */ 
    public function create(Request $request, UserInterface $user){
        $task = new Task();
        //Crear formulario asociado al objeto task
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $task->setCreatedAt(new \DateTime('now'));
            $task->setUser($user);
            $task->setEmail($task->getEmail());
            //Guardar tarea
            $em = $this->getDoctrine()->getManager();
			$em->persist($task);
            $em->flush();
            // Redirección a task index
            return $this->redirect($this->generateUrl('detail',['id'=>$task->getId()]));
        }
        return $this->render('task/create&edit_task.html.twig',[
            'edit' => false,
            'form' => $form->createView()
        ]);

    }

     /**
       * @param=Request formulario modificar tarea, Objeto user autenticado
       * @return=Form Create&edit Task edit=true
     * @Route("/task/update/{id}", name="update_task")
     */ 
    public function update(Request $request, Task $task, UserInterface $user){
        //Verificación existencia Usuario y user propietario tarea o user asignado a tarea o role = USER_ADMIN
        if($user && ($user->getId()==$task->getUser()->getId() or $task->getEmail()==$user->getEmail() or $user->getRole()=="ROLE_ADMIN")){
            //Crear formulario asociado al objeto task, al asociarlo, ya se rellenan los campos del formulario automáticamente
            //Con los valores del objeto $task recibido por parámetro
            $form = $this->createForm(TaskType::class, $task);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                //Guardar tarea
                $em = $this->getDoctrine()->getManager();
                $em->persist($task);
                $em->flush();

                // Redirección a detalle tarea modificada
                return $this->redirect($this->generateUrl('detail',['id'=>$task->getId()]));
            }

            return $this->render('task/create&edit_task.html.twig',[
                'edit' => true,
                'form' => $form->createView()
            ]);
        }else{
            // Redirección a task index
			return $this->redirectToRoute('task');
        }
    }

    /**
       * @param=Tarea, Objeto user autenticado
     * @Route("/task/delete/{id}", name="delete_task")
     */ 
    public function delete(Task $task, UserInterface $user){
        //Verificación existencia Tarea Y Usuario y user propietario tarea o user asignado a tarea o role = USER_ADMIN
        if($task && $user && ($user->getId()==$task->getUser()->getId() or $task->getEmail()==$user->getEmail() or $user->getRole()=="ROLE_ADMIN")){
            $em=$this->getDoctrine()->getManager();
            $em->remove($task);        
            $em->flush();
        }else{
            // Redirección a task index
			return $this->redirectToRoute('task');
        }

         // Redirección a task index
			return $this->redirectToRoute('task');

    }


    /**
       * @param=Tarea, user autenticado
       * @return=View Index
     * @Route("/task/update_email/{id}", name="update_email_task")
     */ 
    public function updateEmail(Task $task, UserInterface $user){
           //Verificación existencia Tarea Y Usuario 
            if($task && $user){
                //Asignar email del usuario a la tarea
                $task->setEmail($user->getEmail());
                //Guardar tarea
                $em = $this->getDoctrine()->getManager();
                $em->persist($task);
                $em->flush();
                // Redirección a detalle tarea modificada
                return $this->redirect($this->generateUrl('detail',['id'=>$task->getId()]));
            }else{
                // Redirección a task index
                return $this->redirectToRoute('task');
            }

         // Redirección a task index
			return $this->redirectToRoute('task');
    }
    
}
