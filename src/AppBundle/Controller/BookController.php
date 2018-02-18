<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Entity\Genre;
use AppBundle\Form\BookFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class BookController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
    	$books = $this->getDoctrine()
		    ->getRepository('AppBundle:Book')
		    ->findAll();

	    /**
	     * @var $paginator \Paginator_ded53a1
	     */
    	$paginator = $this->get('knp_paginator');
    	$pagination = $paginator->paginate(
    	    $books,
	        $request->query->getInt('page', 1),
	        9
	    );

        return $this->render('Book/index.html.twig', [
        	'pagination' => $pagination,
        	'books' => $books
        ]);
    }

    /**
     * @Route("/book/create", name="book_create")
     */
    public function createAction(Request $request) {
    	$book = new Book;
		$genres = $this->getDoctrine()->getRepository('AppBundle:Genre')->findAll();

    	$form = $this->createForm(BookFormType::class, $book);

//		$form['Genre']->setData($genres);

    	$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()) {
			$title = $form['Title']->getData();
			$coverImage = $form['coverImage']->getData();
			$author = $form['Author']->getData();
			$publishDate = $form['PublishDate']->getData();
			$genre = $form['Genre']->getData();

			$book->setTitle($title);
			$book->setCoverImage($coverImage);
			$book->setAuthor($author);
			$book->setPublishDate($publishDate);
			$book->setGenre($genre);

			$em = $this->getDoctrine()->getManager();

			$em->persist($book);
			$em->flush();

			$this->addFlash(
				'notice',
				'Post added'
			);

			return $this->redirectToRoute('book_create');
		}
	    return $this->render('Book/create.html.twig', [
		    'form' => $form->createView()
	    ]);
    }


	/**
	 * @Route("/book/details/{id}", name="book_details")
	 */
    public function detailsAction($id) {
    	$book = $this->getDoctrine()
	                 ->getRepository("AppBundle:Book")
	                 ->find($id);

    	return $this->render("Book/details.html.twig", [
    		'book' => $book
	    ]);
    }
}
