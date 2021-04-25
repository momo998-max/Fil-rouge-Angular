<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Agence;
use App\Entity\Compte;
use App\Entity\Transaction;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Projet Back');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Agence', 'fas fa-landmark', Agence::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user-friends', User::class);
        yield MenuItem::linkToCrud('Comptes', 'fas fa-money-check-alt', Compte::class);
        yield MenuItem::linkToCrud('Transactions', 'fas fa-hand-holding-usd', Transaction::class);
    }
}
