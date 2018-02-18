<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Entity\User;
use AppBundle\Entity\Genre;
use AppBundle\Form\BookFormType;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class BookController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
	    $searchQuery = $request->query->get('search');

	    if ($searchQuery !== null) {
	    	$books = $this->getDoctrine()
			              ->getRepository('AppBundle:Book')
			              ->searchBooksByTitle($searchQuery);
	    } else {
	    	$books = $this->getDoctrine()
		                  ->getRepository('AppBundle:Book')
		                  ->findAll();
	    }

	    /**
	     * @var Paginator $paginator
	     */
    	$paginator = $this->get('knp_paginator');
    	$pagination = $paginator->paginate(
    	    $books,
	        $request->query->getInt('page', 1),
	        9,
	        [
	        	'defaultSortFieldName' => 'title',
	        ]
	    );

        return $this->render('Book/index.html.twig', [
        	'pagination' => $pagination,
        ]);
    }

	/**
	 * @Route("/myCart/", name="my_cart")
	 */
    public function myCartAction(Request $request) {
	    $user = $this->getUser();

    	/**
	     * @var $userBooks User
	     */
    	$userBooks = $user->getBookCollection()->getValues();

    	/**
	     * @var Paginator $paginator
	     */
    	$paginator = $this->get('knp_paginator');
    	$pagination = $paginator->paginate(
    		$userBooks,
		    $request->query->getInt('page', 1 ),
		    9,
	        [
	        	'defaultSortFieldName' => 'title',
	        ]
	    );

    	return $this->render('Book/myCart.html.twig', [
    		'pagination' => $pagination,
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
	 * @Route("/book/cart/add/{id}", name="addToCart")
	 */
    public function addToCartAction($id) {
    	$em = $this->getDoctrine()->getManager();

    	$book = $em->getRepository('AppBundle:Book')
		    ->find($id);

	    /**
	     * @var $user User
	     */
    	$user = $this->getUser();

    	if (!$user->getBookCollection()->contains($book)) {
		    $user->getBookCollection()->add($book);
	    }

	    $em->flush();

	    return $this->redirectToRoute('homepage');
    }

	/**
	 * @Route("/myCart/remove/book/{id}", name="removeFromCart")
	 */
    public function removeBookFromCartAction($id) {
	    /**
	     * @var $user User
	     */
	    $user = $this->getUser();

	    $em = $this->getDoctrine()->getManager();

	    $book = $em->getRepository('AppBundle:Book')
	               ->find($id);

	    $user->getBookCollection()->removeElement($book);

	    $em->flush();

	    return $this->redirectToRoute('my_cart');
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
