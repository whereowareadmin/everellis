<?php

namespace Vicomage\ImportExport\Controller\Adminhtml\Export;

use Magento\Framework\App\Filesystem\DirectoryList;

class Page extends \Vicomage\ImportExport\Controller\Adminhtml\Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */

    public function execute()
    {
        if($this->getRequest()->getParam('theme_path')) $this->ExportXml();
        $resultPage = $this->_resultPageFactory->create();
        return $resultPage;
    }

    public function ExportXml()
    {
        $fileName = 'page.xml';
        $theme_path = $this->getRequest()->getParam('theme_path');
        $exportIds = $this->getRequest()->getParam('exportIds');
        $dir = $this->_filesystem->getDirectoryWrite(DirectoryList::APP);
        $filePath = sprintf(self::CMS, strtolower($theme_path)) .$fileName;
        try{
                $collection = $this->_objectManager->create('\Magento\Cms\Model\ResourceModel\Page\Collection');
                $collection->addFieldToFilter('page_id',array('in'=>$exportIds));
                $options = array('title', 'page_layout', 'identifier', 'content_heading', 'content', 'layout_update_xml', 'is_active');
                $xml = '<?xml version="1.0" encoding="UTF-8"?>';
                $xml .= '<root>';
                    $xml.= '<page>';
                    $num = 0;
                    foreach ($collection as $menu) {
                        $xml.= '<item>';
                        foreach ($options as $opt) {
                            $xml.= '<'.$opt.'><![CDATA['.$menu->getData($opt).']]></'.$opt.'>';
                        }
                        $xml.= '</item>';
                        $num++;
                    }
                    $xml.= '</page>';
                $xml.= '</root>';
               // $this->_sendUploadResponse($fileName, $xml);
                $dir->writeFile($filePath, '$xml');
                $backupFilePath = $dir->getAbsolutePath($filePath);
                // @mkdir($fi   lePath, 0644, true);
                $doc =  new \DOMDocument('1.0', 'UTF-8');
                $doc->loadXML($xml);
                $doc->formatOutput = true;
                $doc->save($backupFilePath);

                $this->messageManager->addSuccess(__('Export (%1) Item(s):', $num));
                $this->messageManager->addSuccess(__('Successfully exported to file "%1"',$backupFilePath));
        } catch (\Exception $e) {
                $this->messageManager->addError(__('Can not save export file "%1".<br/>"%2"', $filePath, $e->getMessage()));
        }
    }
}
