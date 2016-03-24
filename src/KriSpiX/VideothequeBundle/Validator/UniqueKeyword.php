<?php

namespace KriSpiX\VideothequeBundle\Validator;

use Doctrine\ORM\Event\LifecycleEventArgs;
/*
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
*/
use KriSpiX\VideothequeBundle\Entity\Movie;
use KriSpiX\VideothequeBundle\Entity\Keyword;

class UniqueKeyword
{
    /*
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();
        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof Keyword) {
                $md = $em->getClassMetadata(get_class($entity));
                $uow->recomputeSingleEntityChangeSet($md, $entity);
                $em->clear($entity);
            }
        }
    }
    */
    /**
     * This will be called on newly created entities
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        
        $entity = $args->getEntity();
        
        if ($entity instanceof Movie) {
            
            $entityManager = $args->getEntityManager();
            $keywords = $entity->getKeywords();

            foreach($keywords as $key => $keyword){
                // let's check for existance of this keyword
                $results = $entityManager->getRepository('KriSpiXVideothequeBundle:Keyword')->findBy(array('name' => $keyword->getName()), array('id' => 'ASC') );
                
                // if keyword exists use the existing keyword
                if (count($results) > 0) {
                    $keywords[$key] = $results[0];
                }
            }
        } 
        

        /*elseif ($entity instanceof Keyword) {
            $entityManager = $args->getEntityManager();
            $keyword = $entity->getName();
            $results = $entityManager->getRepository('KriSpiXVideothequeBundle:Keyword')->findBy(array('name' => $keyword), array('id' => 'ASC') );
            if (count($results) > 0){
                $entityManager->clear($entity);
            }
        } */
    }

    /**
     * Called on updates of existent entities
     *  
     * New keywords were already created and persisted (although not flushed)
     * so we decide now wether to add them to Movies or delete the duplicated ones
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Movie) {
            
            $entityManager = $args->getEntityManager();
            $keywords = $entity->getKeywords();

            foreach($keywords as $keyword) {

                // let's check for existance of this keyword
                // find by name and sort by id keep the older keyword first
                $results = $entityManager->getRepository('KriSpiXVideothequeBundle:Keyword')->findBy(array('name' => $keyword->getName()), array('id' => 'ASC'));
                // if keyword exists at least two rows will be returned
                // keep the first and discard the second
                if (count($results) > 1) {
                    $knownKeyword = $results[0];
                    $entity->addKeyword($knownKeyword);

                    // remove the duplicated keyword
                    $duplicatedKeyword = $results[1];
                    $entityManager->remove($duplicatedKeyword);
                    //$entity->addKeyword();

                } else {
                    // keyword doesn't exist yet, add relation
                    //$entity->addKeyword($keyword);
                }
            }
        }
        
    }
}