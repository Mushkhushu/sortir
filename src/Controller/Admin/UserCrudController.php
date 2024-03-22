<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserCSVType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;


class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex()->hideOnForm(),
            TextField::new('username'),
            TextField::new('firstName'),
            TextField::new('lastName'),
            TextField::new('phoneNumber'),
            TextField::new('mail'),
            TextEditorField::new('password')->onlyOnForms(),
            AssociationField::new('site')->setLabel('Site'),
            ImageField::new('picture')->setLabel('Avatar')->setBasePath('/uploads')->setUploadDir('public/uploads'),
            BooleanField::new('isActive', 'actif/inactif'),
            ChoiceField::new('roles')->setLabel('Roles')->setChoices([
                'User' => 'ROLE_USER',
                'Admin' => 'ROLE_ADMIN',
            ])->allowMultipleChoices(),
        ];
    }
}