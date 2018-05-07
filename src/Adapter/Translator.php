<?php


namespace Payone\Adapter;


use Payone\PluginConstants;
use Plenty\Plugin\Translation\Translator as PlentyTranslator;

class Translator
{

    /**
     * @var PlentyTranslator
     */
    private $translator;

    /**
     * @param PlentyTranslator $translator
     */
    public function __construct(PlentyTranslator $translator)
    {
        $this->translator = $translator;
    }


    /**
     * @param string $id filename + '.' + key
     * @param array $parameters
     * @param string|null $locale
     * @return string
     */
    public function trans(
        string $id,
        array $parameters = [],
        string $locale = null
    ) {
        return $this->translator->trans(PluginConstants::NAME.'::'.$id, $parameters, $locale);
    }
}