<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Services\TransactionCode;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TransactionController extends AbstractController
{
    /**
     * @Route("/transaction", name="transaction")
     */
    public function index(): Response
    {
        return $this->render('transaction/index.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }

    /**
    * @Route(
    *     name="retrait",
    *     path="/user/retrait/{id}",
    *     methods={"PUT"},
    *     defaults={
    *         "_controller"="\app\Controller\TransactionController::__invoke",
    *         "_api_resource_class"=Transaction::class,
    *         "_api_item_operation_name"="retrait"
    *     }
    * )
    */
    public function __invoke(Request $request, EntityManagerInterface $manager, Security $security, TransactionCode $trans, ClientRepository $client_repo, TransactionRepository $transact_repo, $id){

        if (!$data = $transact_repo->find($request->attributes->get('data')->getId())) {
            return $this->json(['infos'=>'Transaction introuvable'], 404);
        }
        if ($data->getWithdrawer()!==null) {
            return $this->json(['infos'=>'Cette transaction est déjà validée'], 400);
        }
        if ($data->getEtat()==='annulé') {
            return $this->json(['infos'=>'Cette transaction a été annulée'], 400);
        }
        $user = $security->getUser();
        $data->setWithdrawer($user);
        $data->setEtat('retiré');
        $data->setRetiredAt(new \DateTime());
        $compteRetrait = $user ->getAgence() ->getCompte();
        
        $compteRetrait->setSolde($data->getMontant());
        $data->setCompteRetrait($compteRetrait);

        if ($from = $client_repo->findOneByIdCard($data->getSendFrom()->getIdCard())) {
            $data->setSendFrom($from);
        }
        if ($to = $client_repo->findOneByPhone($data->getSendTo()->getTelephone())) {
            $to ->setIdCard($data->getSendTo()->getIdCard());
            $data->setSendTo($to);
        }

         /** Envoi SMS */
        // $trans->retraitArgentSMS($data->getSendFrom()->getTelephone(), $data->getSendTO()->getFirstName().' '.$data->getSendTo()->getLastName(),$data->getMontant());

        $manager->persist($data);
        $manager->flush();

        return $this->json(['infos'=>'Opération reussie'], 200);
    }
}
