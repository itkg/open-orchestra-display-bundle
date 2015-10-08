<?php

namespace OpenOrchestra\DisplayBundle\DisplayBlock\Strategies;

use OpenOrchestra\DisplayBundle\Exception\NodeNotFoundException;
use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use OpenOrchestra\ModelInterface\Repository\ReadNodeRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseBundle\Manager\TagManager;

/**
 * Class SubMenuStrategy
 */
class SubMenuStrategy extends AbstractStrategy
{
    const SUBMENU = 'sub_menu';

    protected $nodeRepository;
    protected $request;
    protected $tagManager;

    /**
     * @param ReadNodeRepositoryInterface $nodeRepository
     * @param RequestStack                $requestStack
     * @param TagManager                  $tagManager
     */
    public function __construct(
        ReadNodeRepositoryInterface $nodeRepository,
        RequestStack $requestStack,
        TagManager $tagManager
    ){
        $this->nodeRepository = $nodeRepository;
        $this->request = $requestStack->getCurrentRequest();
        $this->tagManager = $tagManager;
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
        return self::SUBMENU == $block->getComponent();
    }

    /**
     * Indicate if the block is public or private
     *
     * @param ReadBlockInterface $block
     *
     * @return bool
     */
    public function isPublic(ReadBlockInterface $block)
    {
        return true;
    }

    /**
     * Perform the show action for a block
     *
     * @param ReadBlockInterface $block
     *
     * @return Response
     *
     * @throws NodeNotFoundException
     */
    public function show(ReadBlockInterface $block)
    {
        $nodes = $this->getNodes($block);

        if (!is_null($nodes)) {
            return $this->render(
                'OpenOrchestraDisplayBundle:Block/Menu:tree.html.twig',
                array(
                    'tree' => $nodes,
                    'id' => $block->getId(),
                    'class' => $block->getClass(),
                )
            );
        }

        throw new NodeNotFoundException($block->getAttribute('nodeName'));
    }

    /**
     * Get nodes to display
     * 
     * @param ReadBlockInterface $block
     *
     * @return array
     */
    protected function getNodes(ReadBlockInterface $block)
    {
        $nodes = null;
        $nodeName = $block->getAttribute('nodeName');
        $siteId = $this->currentSiteManager->getCurrentSiteId();

        if (!is_null($nodeName)) {
            $nodes = $this->nodeRepository->getSubMenu($nodeName, $block->getAttribute('nbLevel'), $this->request->getLocale(), $siteId);
        }

        return $nodes;
    }

    /**
     * Return block specific cache tags
     * 
     * @param ReadBlockInterface $block
     * 
     * @return array
     */
    public function getCacheTags(ReadBlockInterface $block)
    {
        $tags = array();

        $nodes = $this->getNodes($block);

        if ($nodes) {
            foreach ($nodes as $node) {
                $tags[] = $this->tagManager->formatNodeIdTag($node->getNodeId());
            }
        }

        return $tags;
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'sub_menu';
    }
}
