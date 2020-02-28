<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleAdminController extends BaseController
{
    /**
     * @Route("/admin/article/new", name="admin_article_new")
     * @IsGranted("ROLE_ADMIN_ARTICLE")
     */
    public function new(EntityManagerInterface $em, Request $request) // Creates new articles, handles article requests and saves data to the database
    {
        // Rendering the form when the the user hits this functions route
        $form = $this->createForm(ArticleFormType::class); // Creating the form by using the createForm() method and passing it the form we want it to build, which is the ArticleFormType class


        // There are 3 possible flows for the code below
        // 1. If the object $request is a GET request, isSubmitted() returns false, and the form is passed to twig.
        // 2. If the object $request is a POST request but validation fails, isValid() will return false
        // and the form is again passed to twig. But now it renders with the validation errors
        // 3. If the object $request is a POST request and validation passes,
        // both isSubmitted() and isValid() are true and we finally get into the if block.

        $form->handleRequest($request); // By default the handleRequest() method only processes the data when the $request is a POST request (When the form is being submitted)
                                        // When the form is originally loaded handleRequest will see that it is a GET request for the form and does nothing.

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData(); // When isSubmitted() and isValid() are true, we get to this code which is
                                        // how we access the final, normalized data that was submitted and setting it to $data

            $em->persist($article); // Saving the $article object we just created
            $em->flush();

            // Creating a Flash message when a new article has been created.
            // This method is a shortcut to add a flash message into the current session
            // But flash messages are special, they only live in the session until they are read for the first time
            // As soon as the message appears on the page and we read it, it will disappear. It is the perfect way to store temp messages
            $this->addFlash('success', 'Article Created'); // Passing the flash message type as 'success' which will be used to read and render the message. It can be found in base.html.twig

            return $this->redirectToRoute('admin_article_list'); // After the form has been successfully submitted and saved, redirecting the user to the specified route which has a matching name.
        }

        return $this->render('article_admin/new.html.twig',[ // returning a view with the form passed to it as the variable 'articleForm'. Don't pass the form object directly, always use createView() method
            'articleForm' => $form->createView(),
        ]);

    }

    /**
     * @Route("/admin/article/location-select", name="article_admin_location_select")
     * @IsGranted("ROLE_USER")
     */
    public function getSpecificLocationSelect(Request $request) //Our Javascript sends the location that was just selected to this endpoint and it will return the new html needed for the entire specific location name field
    {
        if (!$this->isGranted('ROLE_ADMIN_ARTICLE') && $this->getUser()->getArticles()->isEmpty()) { // if the user does not have the admin role and is not the author of any articles
            throw $this->createAccessDeniedException(); // Throw an access denied exception
        }

        $article = new Article();
        $article->setLocation($request->query->get('location'));
        $form = $this->createForm(ArticleFormType::class, $article);

        // if there is no field, return an ampty response
        if (!$form->has('specificLocationName')) {
            return new Response(null, 204);
        }

        // if wie do have the filed, return a template and render it
        return $this->render('article_admin/_specific_location_name.html.twig', [
            'articleForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/article/{id}/edit", name="admin_article_edit")
     * @IsGranted("MANAGE", subject="article")
     */
    public function edit(Article $article, Request $request, EntityManagerInterface $em) // Editing an existing form
    {
        // All this code below is identical to the code that creates a new article, apart from one small change.
        $form = $this->createForm(ArticleFormType::class, $article , ['include_published_at' => true]); // Passing $article as the second argument in this method is the small change. Passing an option as the third argument with the value of it set to true
                                                                            // When we pass $article, which is the object we just got from the database, becomes the data attached to the form
                                                                            // This causes two things to happen:
                                                                            // 1. When Symfony renders the form, it calls the getter methods on that article object and uses those values to fill in the fields on the form
                                                                            // 2. When the form is submitted, it calls the setter methods on that same article object
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($article);
            $em->flush();
            $this->addFlash('success', 'Article Updated!');
            return $this->redirectToRoute('admin_article_edit', [
                'id' => $article->getId() // We have to pass the article ID in the redirect because we are directing it to this methods route and it requires it.
            ]);
        }
        return $this->render('article_admin/edit.html.twig', [
            'articleForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/article", name="admin_article_list")
     */
    public function list(ArticleRepository $articleRepository) // Fetches all the articles in the database and displays them
    {
        $articles = $articleRepository->findAll(); // Setting $articles to the articleRepository function findAll() which queries the database to find all the articles.

        return $this->render('article_admin/list.html.twig', [ // Rendering the admin list view, which will display all the articles, published and unpublished
            'articles' => $articles // Passing this view the $articles object as the variable 'articles'
        ]);
    }
}
