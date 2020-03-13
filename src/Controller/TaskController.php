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
        //Guardar todos las tareas del usuario en array de objetos tasks  
         $tasks=$user->getTasks();
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
            //Guardar tarea
            $em = $this->getDoctrine()->getManager();
			$em->persist($task);
            $em->flush();

            // Redirección a task index
            return $this->redirect($this->generateUrl('detail',['id'=>$task->getId()]));
        }

        return $this->render('task/create_task.html.twig',[
            'form' => $form->createView()
        ]);

    }

    
}
