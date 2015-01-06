<?php

class CM_Http_Response_Page_Embed extends CM_Http_Response_Page {

    /** @var string|null */
    private $_title;

    /**
     * @throws CM_Exception_Invalid
     * @return string
     */
    public function getTitle() {
        if (null === $this->_title) {
            throw new CM_Exception_Invalid('Unprocessed page has no title');
        }
        return $this->_title;
    }

    /**
     * @param CM_Page_Abstract $page
     * @return string
     */
    protected function _renderPage(CM_Page_Abstract $page) {
        $renderAdapterLayout = new CM_RenderAdapter_Layout($this->getRender(), $page);
        $this->_title = $renderAdapterLayout->fetchTitle();
        return $renderAdapterLayout->fetchPage();
    }

    protected function _process() {
        if ($this->_request->getLanguageUrl() && $this->getViewer()) {
            $this->_request->setPathParts($this->_request->getPathParts());
            $this->_request->setLanguageUrl(null);
            $path = CM_Util::link($this->_request->getPath(), $this->_request->getQuery());
            $this->redirectUrl($this->getRender()->getUrl($path, $this->_site));
        } else {
            $this->getRender()->getServiceManager()->getTrackings()->trackPageView($this->getRender()->getEnvironment());
            $html = $this->_processPageLoop($this->getRequest());
            $this->_setContent($html);
        }
    }
}
