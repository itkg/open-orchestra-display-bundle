<?php

namespace OpenOrchestra\DisplayBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

/**
 * Class OrchestraUrlExtension
 */
class OrchestraUrlExtension extends \Twig_Extension
{
    protected $urlGenerator;

    /**
     * @param $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('orchestraUrl', array($this, 'orchestraUrl'))
        );
    }

    /**
     * Generate an url and catch mandatory exceptions if asked for it
     *
     * @param string $route
     * @param array  $parameters
     *
     * @return string
     */
    public function orchestraUrl($route, $parameters = array(), $catchMandatoryException = false)
    {
        try {
            return $this->urlGenerator->generate($route, $parameters);
        } catch (MissingMandatoryParametersException $e) {
            if ($catchMandatoryException) {
                return false;
            } else {
                throw($e);
            }
        }
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'orchestra_url';
    }
}