<?php

namespace Application\Library\I18n\Translator;

class Translator extends \Zend\I18n\Translator\Translator {

    /**
     * @var \Zend\Log\Logger
     */
    protected $log = null;
    
    public function translate($message, $textDomain = 'default', $locale = null) {
        $locale = ($locale ? : $this->getLocale());
        $translation = $this->getTranslatedMessage($message, $locale, $textDomain);

        if ($translation !== null && $translation !== '') {
            return $translation;
        }

        if (null !== ($fallbackLocale = $this->getFallbackLocale())
                && $locale !== $fallbackLocale
        ) {
            return $this->translate($message, $textDomain, $fallbackLocale);
        }
        if(!is_null($this->log)){
            $this->log->warn("Missing translate for __'{$message}'__ in textDomain: '{$textDomain}' and locale: '{$locale}'.");
        }
        return $message;
    }
    
    public function setLog(\Zend\Log\Logger $log){
        $this->log = $log;
    }

}