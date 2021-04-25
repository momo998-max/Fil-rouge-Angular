<?php

namespace App\Controller\Admin;

use App\Entity\Agence;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AgenceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Agence::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('adresse','adresse'),
            TextField::new('telephone'),
            TextField::new('nom'),
            NumberField::new('lattitude'),
            NumberField::new('longitude'),
            TextField::new('statut')->hideOnForm(),
            AssociationField::new('admins'),
        ];
    }
    
}
