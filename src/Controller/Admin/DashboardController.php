<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Sorties;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Locale;
use PhpParser\Comment;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


/**
 * @method getAdmin()
 */
class DashboardController extends AbstractDashboardController
{
    #[isGranted("ROLE_ADMIN")]
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        return parent::index();


        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }




    public function erreur()
    {
        // Exemple : vérifie si l'accès est autorisé, sinon lance une exception AccessDeniedException
        if (!$this->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('NON.');
        }


    }


    protected function renderHttpException (\Exception $exception)
    {

        return $this->render('Exception/error403.html.twig', [
            'errorMessage' => $exception->getMessage(),
            'errorImage' => $exception->getStatusCode()
        ], new Response('', $exception->getStatusCode()));
    }






    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

            MenuItem::section('Blog'),
            MenuItem::linkToCrud('Categories', 'fa fa-tags', User::class),
            MenuItem::linkToCrud('Blog Posts', 'fa fa-file-text', Sorties::class),

            MenuItem::section('Users'),
            MenuItem::linkToCrud('Comments', 'fa fa-comment', Comment::class),
            MenuItem::linkToCrud('Users', 'fa fa-user', User::class),
            // links to the 'index' action of the Category CRUD controller
            MenuItem::linkToCrud('Categories', 'fa fa-tags', Category::class),

            // links to a different CRUD action
            MenuItem::linkToCrud('Add Category', 'fa fa-tags', Category::class)
                ->setAction('new'),

            MenuItem::linkToCrud('Show Main Category', 'fa fa-tags', Category::class)
                ->setAction('detail')
                ->setEntityId(1),

            // if the same Doctrine entity is associated to more than one CRUD controller,
            // use the 'setController()' method to specify which controller to use
            MenuItem::linkToCrud('Categories', 'fa fa-tags', Category::class)
                ->setController(LegacyCategoryCrudController::class),

            // uses custom sorting options for the listing
            MenuItem::linkToCrud('Categories', 'fa fa-tags', Category::class)
                ->setDefaultSort(['createdAt' => 'DESC']),

           ]; // you can also specify the actions that should appear in the menu
    }




}