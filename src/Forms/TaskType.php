<?php
namespace App\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

//Clase crear tarea-->Estructura formulario
class TaskType extends AbstractType{
	
	public function buildForm(FormBuilderInterface $builder, array $options){
		$builder->add('title', TextType::class, array(
			'label' => 'Título'
		))
		->add('content', TextareaType::class, array(
			'label' => 'Descripción'
		))
		->add('priority', ChoiceType::class, array(
			'label' => 'Prioridad',
			'choices' => array (
				'alta' => 'alta',
				'media' => 'media',
				'baja' => 'baja',
			)
		))
		->add('hours', TextType::class, array(
			'label' => 'Horas Presupuestadas'
		))
		->add('submit', SubmitType::class, array(
			'label' => 'Crear Tarea'
		));
	}
	
}