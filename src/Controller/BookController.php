<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    /**
     * @Route("/list", name="listAllBooks")
     */
    function list() {
        $entityManager = $this->getDoctrine()->getManager();
        $bookRepo = $entityManager->getRepository(Book::class);
        $results = $bookRepo->findAll();

        return $this->render('base.twig', ['books' => $results]);
    }

    /**
     * @Route("/find", name="searchBooks")
     */
    public function search(Request $request)
    {
        $query = $request->query->get('query');
        $entityManager = $this->getDoctrine()->getManager();
        $bookRepo = $entityManager->getRepository(Book::class);
        $results = $bookRepo->findByTitle($query);

        return $this->render('base.twig', ['books' => $results]);
    }

    /**
     * @Route("/book/{id}", name="bookDetails")
     */
    public function fetch($id, Request $request)
    {
        $edit = false;
        if ($request->query->get('edit') == 1) {
            $edit = true;
        }

        $book = $this->getDoctrine()
            ->getRepository(Book::class)
            ->find($id);

        if (!$book) {
            return $this->render('base.twig', ['error' => 'No Matching Book Found']);
        }

        $result = [];
        $result['id'] = $book->getId();
        $result['title'] = htmlspecialchars($book->getTitle());
        $result['description'] = htmlspecialchars($book->getDescription());
        $result['price'] = $book->getPrice();

        return $this->render('base.twig', ['book' => $result, 'edit' => $edit]);
    }

    /**
     * @Route("/edit", name="updateBook")
     */
    public function updateBook(Request $request)
    {
        $id = $request->request->get('id');
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $price = $request->request->get('price');

        $entityManager = $this->getDoctrine()->getManager();
        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        if ($title) {
            $book->setTitle($title);
        }

        if ($description) {
            $book->setDescription($description);
        }

        if ($price) {
            $book->setPrice($price);
        }

        $entityManager->flush();

        $result = [];
        $result['id'] = $book->getId();
        $result['title'] = htmlspecialchars($book->getTitle());
        $result['description'] = htmlspecialchars($book->getDescription());
        $result['price'] = $book->getPrice();

        return $this->render('base.twig', ['book' => $result, 'edit' => false, 'success' => true]);

    }

    /**
     * @Route("/new", name="book")
     */
    public function newDummy()
    {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to your action: index(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        $book = new Book();
        $book->setTitle('Keyboard Book');
        $book->setPrice(1999);
        $book->setDescription('Ergonomic and stylish book!');

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($book);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new product with id ' . $book->getId());
    }

}
