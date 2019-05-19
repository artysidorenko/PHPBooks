<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
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
    public function newBook(Request $request)
    {
        // creates a task and gives it some dummy data for this example
        $book = new Book();
        $book->setTitle('Write a blog post');
        $book->setDescription('test');
        $book->setPrice(15);

        $form = $this->createFormBuilder($book)
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('price', IntegerType::class)
            ->add('save', SubmitType::class, ['label' => 'Add New Book'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$book` variable has also been updated
            $book = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Book is a Doctrine entity, save it!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->render('base.twig', ['book' => $result, 'edit' => false, 'success' => true]);

        }

        return $this->render('new.twig', [
            'form' => $form->createView(),
        ]);

    }

}
