<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Task;

class TaskController extends AbstractController
{
    /**
     * @Route("/", name="task")
     */
    public function index()
    {   
        
        //PRUEBA RELACIONES
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

    //Detalle tarea
    //@param=tarea deseada 
    //@return=Vista con objeto tarea 
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
}
