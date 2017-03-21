<?php

namespace OpenOrchestra\DisplayBundle\DisplayBlock\Strategies;

use OpenOrchestra\ModelInterface\Model\ReadBlockInterface;
use OpenOrchestra\ModelInterface\Repository\ReadNodeRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use OpenOrchestra\BaseBundle\Manager\TagManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class MenuStrategy
 */
class MenuStrategy extends AbstractMenuStrategy
{
    const NAME = 'menu';

    protected $nodeRepository;
    protected $tagManager;

    /**
     * @param ReadNodeRepositoryInterface   $nodeRepository
     * @param TagManager                    $tagManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        ReadNodeRepositoryInterface $nodeRepository,
        TagManager $tagManager,
        AuthorizationCheckerInterface $authorizationChecker
    ){
        $this->nodeRepository = $nodeRepository;
        $this->tagManager = $tagManager;
        parent::__construct($authorizationChecker);
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
        return self::NAME == $block->getComponent();
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
     */
    public function show(ReadBlockInterface $block)
    {
        $nodes = $this->getNodes();

        return $this->render(
            'OpenOrchestraDisplayBundle:Block/Menu:tree.html.twig',
            array(
                'tree' => $nodes,
                'id' => $block->getId(),
                'class' => $block->getStyle(),
            )
        );
    }

    /**
     * Get Nodes to display
     *
     * @return array
     */
    protected function getNodes()
    {
        $language = $this->currentSiteManager->getCurrentSiteDefaultLanguage();
        $siteId = $this->currentSiteManager->getCurrentSiteId();
        $nodes = $this->nodeRepository->getMenuTree($language, $siteId);

        return $this->getGrantedNodes($nodes);
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

        $nodes = $this->getNodes();

        $siteId = $this->currentSiteManager->getCurrentSiteId();
        $tags[] = $this->tagManager->formatMenuTag($siteId);

        if ($nodes) {
            foreach ($nodes as $node) {
                $tags[] = $this->tagManager->formatNodeIdTag($node->getNodeId());
            }
        }

        return $tags;
    }

    /**
     * @return array
     */
    public function getBlockParameter()
    {
        return array('request.aliasId');
    }

    /**
     * Get the name of the strategy
     *
     * @return string
     */
    public function getName()
    {
        return 'menu';
    }
}
