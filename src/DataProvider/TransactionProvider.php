<?php 

namespace App\DataProvider;

use App\Entity\Transaction;
use App\Services\TransactionCode;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;

final class TransactionProvider implements ItemDataProviderInterface,ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $_request;
    private $_transact_repo;
    private $_transaction;
    private $_client_repo;
    private $_user_repo;
    private $_security;

    public function __construct(
        RequestStack $request,
        TransactionRepository $transact_repo,
        TransactionCode $trans,
        ClientRepository $client_repo,
        UserRepository $user_repo,
        Security $security
    ){
        $this->_request = $request->getCurrentRequest();
        $this->_transact_repo = $transact_repo;
        $this->_transaction = $trans;
        $this->_client_repo = $client_repo;
        $this->_user_repo = $user_repo;
        $this->_security = $security;
    }


    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Transaction::class === $resourceClass && $operationName !=="get";
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?Transaction
    {
        // Retrieve the blog post item from somewhere then return it or null if not found
        return $this->_transact_repo->find($this->_request->attributes->get('id'));

        // return new Transaction($id);
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        /** L'utilisateur connecté */
        $user = $this->_security -> getUser();
        $idUser = $user->getId('id');
        $idCmpte = $user->getAgence()->getCompte()->getId();

        if($operationName==='getByCode'){
            if (!$tr = $this->_transact_repo->findOneByCode($this->_request->attributes->get('code'))) {
                yield 'infos'=>'code invalide ou introuvable';
            }
            yield $tr;
        }
        
        if ($operationName==="user_compte_transactions") {
            
            $trans = $this->_transact_repo->findByUserAndCompte($idUser, $idCmpte);
            yield $trans;
        }

        if ($operationName==="compte_mes_depots") {
            $trans = $this->_transact_repo->findCompteDepot($idUser, $idCmpte);
            yield $trans;
        }

        if ($operationName==="compte_mes_retraits") {
            $trans = $this->_transact_repo->findCompteRetrait($idUser, $idCmpte);
            yield $trans;
        }

        if ($operationName==="compte_all_transactions") {
            $trans = $this->_transact_repo->findCompteAll($idCmpte);
            yield $trans;
        }

        if ($operationName==="compte_all_retraits") {
            $trans = $this->_transact_repo->findCompteAllRetrait($idCmpte);
            yield $trans;
        }

        if ($operationName==="compte_all_depots") {
            $trans = $this->_transact_repo->findCompteAllDepot($idCmpte);
            yield $trans;
        }

        if($operationName==='calcul_frais'){
            $frais=null;
            $montant = $this->_request->attributes->get('montant');
            if (is_numeric($montant)) {
                $frais = $this->_transaction->frais($montant)['frais'];
            }
            yield 'frais'=>$frais;
        }

        if($operationName==='user_agence_compte'){
            $compte = $user->getAgence()->getCompte();
            yield $compte;
        }

        if($operationName==='user_client_nci'){
            yield $this->_client_repo->findOneByIdCard($this->_request->attributes->get('nci'));
        }

        if($operationName==='user_client_phone'){
            yield $this->_client_repo->findOneByPhone($this->_request->attributes->get('phone'));
        }
        // yield new Transaction(2);
    }
}