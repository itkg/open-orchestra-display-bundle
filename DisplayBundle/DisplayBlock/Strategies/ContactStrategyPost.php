<?php

namespace OpenOrchestra\DisplayBundle\DisplayBlock\Strategies;

use OpenOrchestra\DisplayBundle\Form\Type\ContactType;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ContactStrategyPost
 */
class ContactStrategyPost extends AbstractStrategy
{
    const CONTACT = 'contact';

    protected $formFactory;
    protected $router;
    protected $request;

    /**
     * @param FormFactory           $formFactory
     * @param UrlGeneratorInterface $router
     * @param RequestStack $requestStack
     */
    public function __construct(FormFactory $formFactory, UrlGeneratorInterface $router, RequestStack $requestStack)
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * Check if the strategy support this block
     *
     * @param ReadBlockInterface $block
     *
     * @return boolean
     */
    public function support(ReadBlockInterface $block)
    {
        return self::CONTACT == $block->getComponent();
    }

    /**
     * Perform the show action for a block
     *
     * @param ReadBlockInterface $block
     *
     * @return Response
     */
    public function show(ReadBlockInterface $block)
    {
        $data = array(
            'recipient' => $block->getAttribute('recipient'),
            'signature' => $block->getAttribute('signature'),
        );

        $form = $this->formFactory->create(new ContactType(), $data, array(
            'action' => '',
            'method' => 'POST',
        ));

        return $this->render(
            'OpenOrchestraDisplayBundle:Block/Contact:show.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'contactPost';
    }

}