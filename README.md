# Doctrine2 Uploadable Behavior

This php 5.4+ library provides file uploading trait for Doctrine2 entities.

This library needs Doctrine listener to be activated.

<a name="listeners" id="listeners"></a>
## Listeners

Symfony2:

``` yaml

    # app/config/config.yml
    imports:
        - { resource: ../../vendor/ytko/doctrine-uploadable/config/orm-services.yml }

```

Or Doctrine2 api:


``` php

<?php

$em->getEventManager()->addEventSubscriber(new \Ytko\DoctrineBehaviors\ORM\UploadableListener);

```


## Usage

Define a Doctrine2 entity and use traits:

``` php

<?php

use Doctrine\ORM\Mapping as ORM;
use Ytko\DoctrineBehaviors\Model\Uploadable;

/**
 * @ORM\Entity
 */
class Category
{
    use Uploadable;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $id;
}

```

Inspired by <a href="https://github.com/KnpLabs/DoctrineBehaviors">KnpLabs/DoctrineBehaviors</a>.
