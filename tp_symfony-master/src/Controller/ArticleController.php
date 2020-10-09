<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="article_add")
     */
    public function index(EntityManagerInterface $em, Request $request)
    {
        $article = new Article();
        $form = $this->createFormBuilder($article) //un formulaire possède un token pour régler la sécurité 
                ->add('titre',TextType::class,['attr'=>['placeholder'=>"Entrez le titre de l'article"],'label'=>"Titre de l'article"])
                ->add('auteur',TextType::class,['attr'=>['placeholder'=>"Entrez l'auteur"],'label'=>"L'auteur de l'article"])
                ->add('imageFile',FileType::class,['attr'=>['placeholder'=>"Entrez l'url de l'image", 'required'=>false]])
                // ->add('image',UrlType::class,['attr'=>['placeholder'=>"Entrez l'url de l'image"]])
                ->add('description',TextareaType::class,['attr'=>['placeholder'=>"Entrez la descritpion de l'article"]])
                ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $article->setParution(new \DateTime());
            $em->persist($article);
            $em->flush();
            
            $this->addFlash('succes','Article ajoutée');
           return $this->redirectToRoute("article_list");
        }
        //$em = $this->getDoctrine()->getManager();
        // for($i = 0; $i < 5; $i++){

        //     $article = new Article();
        //     $article->setTitre("Article new $i")
        //             ->setAuteur("Franck $i")
        //             ->setParution(new \DateTime())
        //             ->setImage('https://via.placeholder.com/150')
        //             ->setDescription("Description de l'article new $i");
        //     $em->persist($article);
        // }
        //$em->flush();
              
        return $this->render('article/index.html.twig', [
            'formArticle' =>$form->createView()
        ]);

    }
    /**
     * @Route("/list", name="article_list")
     */
    public function list(Request $request){
        //dd($request->request->get('search'));
        $search = $request->request->get('search');
        $repo = $this->getDoctrine()
                    ->getRepository(Article::class);

        if($search){
            $articles = $repo->query($search);
        }else{

            $articles = $repo->findAll();
        }
        //dd($articles);
        return $this->render('article/list.html.twig',[
            'articles'=>$articles
        ]);
    }

    /**
     * @Route("/update/{id}",name="article_update")
     */
    public function update(Request $request, Article $article, EntityManagerInterface $em){

        // $em = $this->getDoctrine()->getManager();
        // $article = $em->getRepository(Article::class)
        //     ->find($id);
        //$article = $repo->find($id);
        // $this->addFlash('succes','Article modifié avec succès');
        // $article->setTitre('Modification de titre avec parameter convertor');
        // $em->flush();
        // return $this->redirectToRoute('article_list');

        $form = $this->createForm(ArticleType::class,$article);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            return $this->redirectToRoute("article_list");
        }

        return $this->render('article/edit.html.twig',['form_edit'=>$form->createView()]);

    }

    /**
     * @Route("/delete/{id}",name="article_delete")
     */
    public function delete(EntityManagerInterface $em, ArticleRepository $repo,$id){
        // $em = $this->getDoctrine()->getManager();
        // $article = $em->getRepository(Article::class)
        //    ->find($id);
        $this->addFlash('succes','Article supprimé avec succès');
        $article = $repo->find($id);
        $em->remove($article);
        $em->flush();

        return $this->redirectToRoute('article_list');
    }
    
}
