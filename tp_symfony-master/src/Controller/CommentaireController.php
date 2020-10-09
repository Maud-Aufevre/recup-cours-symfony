<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentaireController extends AbstractController
{
    /**
     * @Route("/commentaire", name="commentaire")
     */
    public function index(Request $request,ArticleRepository $repo,EntityManagerInterface $em)
    {
        //$article = $repo->find(3);

        $commentaire = new Commentaire();
        // $commentaire->setAuteur('Malick')
        //             ->setMessage('Article peu développé')
        //             ->setArticle($article);
        // $em->persist($commentaire);
        // $em->flush();
        $form = $this->createForm(CommentaireType::class,$commentaire);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($commentaire);
            $em->flush();
            $this->addFlash('succes','commentaire ajouter');
            return $this->redirectToRoute('list_comment');
        }

        return $this->render('commentaire/index.html.twig', [
            "formComment"=>$form->createView()
        ]);
    }

    /**
     * @Route("/list_c", name="list_comment")
     */
    public function list(CommentaireRepository $repo)
    {
        $commentaires = $repo->findAll();

        return $this->render('commentaire/list.html.twig',[
            'commentaires'=>$commentaires]);
    }

    /**
     * @Route("/update_c/{id}", name="update_comment")
     */
    public function update(Request $request, Commentaire $comment, EntityManagerInterface $em)
    {

        // $comment->setAuteur('Duhamel')
        //         ->setMessage('Cet article dit vrai...');
        // $em->flush();

        // return $this->redirectToRoute('list_comment');

        $form = $this->createForm(CommentaireType::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('succes','commentaire modifié avec succès');
            return $this->redirectToRoute('list_comment');
        }
        return $this->render('commentaire/editComment.html.twig',['form_modifComment'=>$form->createView()]);
    }

    
    /**
     * @Route("/delete_c/{id}", name="delete_comment")
     */
    public function delete(Commentaire $comment, EntityManagerInterface $em)
    {

        $em->remove($comment);
        $em->flush();

        return $this->redirectToRoute('list_comment');
    }
    /**
    *@Route("/arc_comment/{id}",name="arc_comm")
    */
    public function commentByPost(ArticleRepository $repo1,CommentaireRepository $repo,$id)
    {
        
        $commentaires = $repo->getCommentaireByArticle($id);
        $article = $repo1->find($id);

        if(!$commentaires){
            $this->addFlash('succes',"Pas de commentaires pour cet article");
           return $this->redirectToRoute("article_list");
        }

        return $this->render('commentaire/arc_comment.html.twig',[
            'article'=>$article,'commentaires'=>$commentaires]);
    }

}
