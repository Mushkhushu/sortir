<?php

namespace App\Controller\Admin;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sorties;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\UserCSVType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/my_dashboard.html.twig');

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Sortir');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Villes', 'fas fa-list', Ville::class);
        yield MenuItem::linkToCrud('Sorties', 'fas fa-list', Sorties::class);
        yield MenuItem::linkToCrud('Lieus', 'fas fa-list', Lieu::class);
        yield MenuItem::linkToCrud('Users', 'fas fa-list', User::class);
        yield MenuItem::linkToCrud('Sites', 'fas fa-list', Site::class);
        yield MenuItem::linkToRoute('Accueil', 'fa fa-home', 'home_home');

    }
    #[\Symfony\Component\Routing\Attribute\Route('/admin/user/loadcsv', name: 'admin_user_loadcsv')]
    public function chargerCSV(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserCSVType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $csvFile = $formData['csv'];
            try {
                $handle = fopen($csvFile->getRealPath(), 'r');
                if ($handle !== false) {
                    // Skip the header row
                    fgetcsv($handle);
                    while (($record = fgetcsv($handle)) !== false) {
                        // Process each record
                        $user = new User();
                        $user->setUsername($record[2]); // Assuming username is in the third column
                        $user->setRoles(["ROLE_USER"]);
                        $password = "motdepasse";
                        $hashedPassword = $passwordHasher->hashPassword($user, $password);
                        $user->setPassword($hashedPassword);
                        $user->setFirstName($record[2]);
                        $user->setLastName($record[3]);
                        $user->setPhoneNumber($record[6]);
                        $user->setMail($record[5]);
                        $user->setSite($formData['site']);
                        // Persist each record
                        $entityManager->persist($user);
                    }

                    fclose($handle);

                    // Flush once
                    $entityManager->flush();

                    // Optionally return or do something with the processed data
                    $this->addFlash('success', "PLEIN de participants ajoutés !");
                    return $this->redirectToRoute('user/index');
                } else {
                    throw new \Exception("Failed to open CSV file");
                }
            } catch (\Exception $e) {
                return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
            }
        }

        return $this->render('admin/user/loadcsv.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
