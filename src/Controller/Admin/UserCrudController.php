<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserCSVType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use League\Csv\Reader;
use League\Csv\Statement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    #[Route('/admin/user/loadcsv', name: 'admin_user_loadcsv')]
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
                    return $this->redirectToRoute('admin_user_loadcsv');
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
