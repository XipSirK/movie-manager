<?php
namespace KriSpiX\VideothequeBundle\Antispam;

class KrispixAntispam extends \Twig_Extension
{
    public function __construct(\Swift_Mailer $mailer, $minLength)
    {
        $this->mailer   = $mailer;
        $this->minLength= (int) $minLength;
    }
    
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
    
    /**
    * Vérifie si le texte est un spam
    *
    * @param string $text
    * @return bool
    */
    public function isSpam($text)
    {
        return strlen($text) < $this->minLength;
    }
    
    public function getFunctions()
    {
        return array(
            'checkIfSpam' => new \Twig_Function_Method($this, 'isSpam')
        );
    }
    
    public function getName()
    {
        return 'KriSpiXAntispam';
    }
}