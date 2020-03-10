<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Task;

class TaskController extends AbstractController
{
    /**
     * @Route("/task", name="task")
     */
    public function index()
    {   
        /*
        //PRUEBA RELACIONES
        //Entity Manager
        $em = $this->getDoctrine()->getManager();
        //Repositorio (clase)
        $task_repo=$this->getDoctrine()->getRepository(Task::class);
        //Guardar todos las tareas en array de objetos tasks
        $tasks=$task_repo->findAll();

        foreach($tasks as $task){
            echo $task->getUser()->getName();
        }
        */
        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }
}
