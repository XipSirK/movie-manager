<?php

namespace KriSpiX\VideothequeBundle\Entity;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Antiflood extends Constraint
{
    public $message = "Vous avez déjà posté un film il y a moins de 15 secondes, merci d'attendre un peu.";

    public function validateBy()
    {
        return 'krispix_videotheque_antiflood';
    }
}