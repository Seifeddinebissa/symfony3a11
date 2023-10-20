<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/addBook', name: 'app_book_add')]
    public function addBook(Request $req,ManagerRegistry $manager) {
        $book = new Book();
        $form = $this->createForm(BookType::class,$book);
        $form->handleRequest($req);
        //$book->setRef($form->getData()->getRef());
        if($form->isSubmitted()){
            $book->setPublished(true);
        $manager->getManager()->persist($book);
        $manager->getManager()->flush();
        return $this->redirectToRoute('app_book_list');
        }
        return $this->render('book/add.html.twig',[
            'f'=>$form->createView()
        ]);
    }

    #[Route('/listBook',name:'app_book_list')]
    public function listBook(BookRepository $repo){
        $book = new Book();
        $pub = 0;
        $notPub = 0;
        $books = $repo->findAll();
        foreach($books as $book){
            if($book->isPublished() == 1){
                $pub++;
            }else{
                $notPub++;
            }
        }
        return $this->render('book/list.html.twig',['books'=>$books,'pub'=>$pub,'notPub'=>$notPub]);
    }
    #[Route('book/delete/{id}',name:'app_book_delete')]
    public function delete($id,ManagerRegistry $manager,  BookRepository $repo){
        $book = $repo->find($id);
        $manager -> getManager()-> remove($book);
        $manager -> getManager()-> flush();
        return $this->redirectToRoute('app_book_list');
    }

    #[Route('/showBook/{ref}',name:'app_showBook')]
    public function showBook ( $ref,BookRepository $repo){
        $book = $repo->find($ref);
        return $this->render('book/show.html.twig',[
            'book'=>$book
        ]);
    }
}
