<?php

/*
 * This file is part of the YtkoDoctrineBehaviors package.
 *
 * (c) Ytko <http://ytko.ru/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ytko\DoctrineBehaviors\ORM;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs,
    Doctrine\Common\EventSubscriber,
    Doctrine\ORM\Events,
    Doctrine\ORM\Mapping\ClassMetadata;

/**
 * Uploadable listener.
 *
 * Adds mapping to the uploadable entites.
 */
class UploadableListener implements EventSubscriber
{
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();

        if (null === $classMetadata->reflClass) {
            return;
        }

        if ($this->isEntitySupported($classMetadata)) {
            if ($classMetadata->reflClass->hasMethod('upload')) {
                $classMetadata->addLifecycleCallback('upload', Events::postPersist);
                $classMetadata->addLifecycleCallback('upload', Events::postUpdate);
            }

            if ($classMetadata->reflClass->hasMethod('removeUpload')) {
                $classMetadata->addLifecycleCallback('removeUpload', Events::postRemove);
            }
        }
    }

    public function getSubscribedEvents()
    {
        return [Events::loadClassMetadata];
    }

    /**
     * Checks whether provided entity is supported.
     *
     * @param ClassMetadata $classMetadata The metadata
     *
     * @return Boolean
     */
    private function isEntitySupported(ClassMetadata $classMetadata)
    {
        $traitNames = $classMetadata->reflClass->getTraitNames();

        return in_array('Ytko\DoctrineBehaviors\Model\Uploadable', $traitNames);
    }
}
