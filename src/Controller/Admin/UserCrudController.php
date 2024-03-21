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


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('username'),
            TextField::new('firstName'),
            TextField::new('lastName'),
            TextField::new('phoneNumber'),
            TextField::new('mail'),
            AvatarField::new('picture')->setLabel('Avatar'),
            BooleanField::new('isActive', 'actif/inactif'),
            ChoiceField::new('roles')->setLabel('Roles')->setChoices([
                'User' => 'ROLE_USER',
                'Admin' => 'ROLE_ADMIN',
            ])->allowMultipleChoices(),
        ];
    }
}