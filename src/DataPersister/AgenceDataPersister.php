<?php

namespace App\DataPersister;

use App\Entity\Agence;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

final class AgenceDataPersister implements ContextAwareDataPersisterInterface
{
    private $_entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->_entityManager = $entityManager;
    }
    
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Agence && ((isset($context['collection_operation_name']) && $context['collection_operation_name']==='creer_agence') || isset($context['item_operation_name']) && ($context['item_operation_name']==='bloquer_agence' || $context['item_operation_name']==='debloquer_agence'));
    }

    public function persist($data, array $context = [])
    {
        if (isset($context['collection_operation_name']) && $context['collection_operation_name']==='creer_agence') {
            if (!$data->getAdministrateur()->getTelephone()) {
                return new Response(
                    'Le numero de telephone est requis',
                    Response::HTTP_FORBIDDEN,
                    ['content-type' => 'text/plain']
                );
            }
            if (!$data->getAdministrateur()->getPrenom()) {
                return new Response(
                    'Le prénom est requis',
                    Response::HTTP_FORBIDDEN,
                    ['content-type' => 'text/plain']
                );
            }
            $data->getAdministrateur()->setRoles('ROLE_ADMINAGENCE');
            $data->addUtilisateur($data->getAdministrateur());
            if ($data->getCompte()->getSolde()<700000.0) {
                return new Response(
                        'A la création, le compte doit avoir au minimum 700000F',
                        Response::HTTP_FORBIDDEN,
                        ['content-type' => 'text/plain']
                    );
            }
        }

        if (isset($context['item_operation_name']) && $context['item_operation_name']==='bloquer_agence') {
            $data->setStatut('bloqué');
            $data->getAdministrateur()->setStatut('bloqué');
            $data->getCompte()->setStatut('bloqué');
            foreach ($data->getUtilisateurs() as $value) {
            $value->setStatut('bloqué');
            }
        }
        if (isset($context['item_operation_name']) && $context['item_operation_name']==='debloquer_agence') {
            $data->setStatut('actif');
            $data->getAdministrateur()->setStatut('actif');
            $data->getCompte()->setStatut('actif');
            foreach ($data->getUtilisateurs() as $value) {
            $value->setStatut('actif');
            }
        }
        // dd($data);
        $this->_entityManager->persist($data);
        $this->_entityManager->flush();
        return $data;
    }

    public function remove($data, array $context = [])
    {
        // return null
    }
}