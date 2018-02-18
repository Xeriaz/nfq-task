<?php

namespace AppBundle\Form;

use AppBundle\Entity\Book;
use AppBundle\Entity\Genre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class BookFormType extends AbstractType {

	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add("Title", TextType::class, [
				'attr' => [
					'class' => 'form-control',
					'style' => 'margin-bottom:15px',
//					'constraints' => [new Length(['min' => 3, 'max' => 255 ])]
				]
			])
	        ->add("coverImage", TextType::class, [
	            'attr' => [
	                'class' => 'form-control',
			        'style' => 'margin-bottom:15px',
//			        'constraints' => [new Length(['min' => 3, 'max' => 255])]
		        ]
	        ])
			->add("Author", TextType::class, [
				'attr' => [
					'class' => 'form-control',
					'style' => 'margin-bottom:15px',
//					'constraints' => [new Length(['min' => 3, 'max' => 255 ])]
				]
			])
			->add("PublishDate", DateType::class, [
				'widget' => 'single_text',
				'attr' => [
					'class' => 'form-control',
					'style' => 'margin-bottom:15px'
				]
			])
			->add("Genre", EntityType::class, [
				'class' => Genre::class,
				'choice_label' => 'Genre',
				'attr' => [
					'class' => 'form-control',
					'style' => 'margin-bottom:15px'
				]
			])
			->add('Post it', SubmitType::class, [
				'label' => 'Create Post',
				'attr'  => [
					'class' => 'btn btn-primary',
					'style' => 'margin-bottom:15px'
				]
			]);
	}

	public function configureOptions( OptionsResolver $resolver ) {

		$resolver->setDefaults([
			'data_class' => Book::class,
		]);

	}

}