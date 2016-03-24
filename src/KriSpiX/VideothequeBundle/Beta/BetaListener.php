<?php

namespace KriSpiX\VideothequeBundle\Beta;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class BetaListener
{
    // Notre processeur
    protected $betaHTML;

    // La date de fin de la version bêta :
    // - Avant cette date, on affichera un compte à rebours (J-3 par exemple)
    // - Après cette date, on n'affichera plus le « bêta »
    protected $endDate;

    public function __construct(BetaHTML $betaHTML, $endDate)
    {
        $this->betaHTML = $betaHTML;
        $this->endDate  = new \Datetime($endDate);
    }

    public function processBeta(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
               
        $remainingDays = $this->endDate->diff(new \Datetime())->format('%d');

        if ($remainingDays <= 0) {
            return;
        }

        $response = $this->betaHTML->displayBeta($event->getResponse(), $remainingDays);
        $event->setResponse($response);
    }
}